<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\DocumentPage;
use App\Models\Documents;
use App\Models\GroupChat;
use App\Models\IsoParagraph;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Smalot\PdfParser\Parser;

class ChatController extends Controller
{
    private function formatGeminiResponse(string $rawAnswer): string
    {
        // Bersihkan block code dari Gemini (```html ... ```)
        $clean = preg_replace('/```(?:html)?|```/', '', $rawAnswer);
        $clean = trim($clean);

        // Jika jawaban sudah HTML rapi (ada <ul> / <li>), langsung return
        if (strpos($clean, '<ul>') !== false || strpos($clean, '<li>') !== false) {
            return $clean;
        }

        // Pisahkan per baris
        $lines = preg_split('/\r\n|\r|\n/', $clean);

        $grouped = [];
        $currentKey = null;

        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '') continue;

            // Cek apakah baris mengandung "Dokumen xxx halaman y"
            if (preg_match('/Dokumen (.+?) halaman (\d+)/i', $line, $matches)) {
                $currentKey = "Dokumen {$matches[1]} halaman {$matches[2]}";

                // Jika key belum ada, buat baru
                if (!isset($grouped[$currentKey])) {
                    $grouped[$currentKey] = [];
                }

                // Ambil sisa teks setelah pola (jika ada) → jadikan poin
                $remaining = trim(str_replace($matches[0], '', $line));
                if ($remaining !== '') {
                    $grouped[$currentKey][] = $remaining;
                }
            } elseif ($currentKey !== null) {
                // Kalau bukan header tapi bagian dari poin dokumen aktif
                $grouped[$currentKey][] = $line;
            }
        }

        // Konversi ke HTML
        $html = '';
        foreach ($grouped as $doc => $points) {
            $html .= "<p><strong>$doc</strong></p><ul>";
            foreach ($points as $p) {
                $html .= '<li>' . e($p) . '</li>';
            }
            $html .= '</ul>';
        }

        return $html !== '' ? $html : nl2br(e($clean));
    }

    public function index(Request $r)
    {
        $data = [
            'pageTitle' => "Chat"
        ];

        return view('main.chat', $data);
    }

    public function send(Request $request)
    {
        if ($request->type == 'Tanya Jawab') {
            $query = trim($request->input('message'));
            if (!$query) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pertanyaan tidak boleh kosong.'
                ]);
            }

            // Ambil semua teks dokumen untuk dictionary typo
            $allTexts = DB::table('document_pages')->pluck('page_text')->implode(' ');
            $allWords = preg_split('/[\s,.\-:;()]+/', mb_strtolower($allTexts, 'UTF-8'));
            $allWords = array_unique(array_filter($allWords));

            $extraWords = ['pribadi', 'privasi', 'password', 'sandi', 'credential', 'login', 'pegawai', 'aset'];
            $dictionary = array_unique(array_merge($allWords, $extraWords));

            // Normalisasi typo
            $words = explode(' ', $query);
            $normalizedWords = [];
            foreach ($words as $word) {
                $closest = $word;
                $shortest = -1;
                foreach ($dictionary as $dictWord) {
                    $lev = levenshtein(mb_strtolower($word, 'UTF-8'), $dictWord);
                    if ($lev == 0) {
                        $closest = $dictWord;
                        $shortest = 0;
                        break;
                    }
                    if ($lev <= $shortest || $shortest < 0) {
                        $closest = $dictWord;
                        $shortest = $lev;
                    }
                }
                $normalizedWords[] = ($shortest > 0 && $shortest <= 2) ? $closest : $word;
            }
            $query = implode(' ', $normalizedWords);

            // Buat embedding pertanyaan
            $queryEmbedding = $this->createEmbedding($query);
            if (!$queryEmbedding) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal membuat embedding pertanyaan.'
                ]);
            }

            // Ambil halaman dokumen yang sudah ada embedding
            $pages = DB::table('document_pages')
                ->join('documents', 'document_pages.document_id', '=', 'documents.id')
                ->whereNotNull('document_pages.embed')
                ->select(
                    'document_pages.id',
                    'document_pages.document_id',
                    'document_pages.page_number',
                    'document_pages.page_text',
                    'document_pages.embed',
                    'documents.title',
                    'documents.file_path'
                )
                ->get();

            // Filter halaman yang relevan dulu
            $keywords = ['password', 'login', 'akun', 'hak akses', 'credential'];
            $containsAny = function (string $haystack, array $needles): bool {
                $hl = mb_strtolower($haystack, 'UTF-8');
                foreach ($needles as $n) {
                    if (mb_strpos($hl, $n) !== false) return true;
                }
                return false;
            };
            $pages = $pages->filter(fn($p) => $containsAny($p->page_text ?? '', $keywords));

            // Jika tidak ada hasil relevan, ambil fallback semua halaman
            if ($pages->isEmpty()) {
                $pages = DB::table('document_pages')
                    ->join('documents', 'document_pages.document_id', '=', 'documents.id')
                    ->select(
                        'document_pages.id',
                        'document_pages.document_id',
                        'document_pages.page_number',
                        'document_pages.page_text',
                        'documents.title',
                        'documents.file_path'
                    )
                    ->take(50)
                    ->get();
            }

            // Hitung skor cosine similarity + boost keyword
            $scored = $pages->map(function ($p) use ($queryEmbedding, $keywords, $containsAny) {
                $pageEmbedding = isset($p->embed) ? json_decode($p->embed, true) : null;
                $sim = $pageEmbedding ? $this->cosineSimilarity($queryEmbedding, $pageEmbedding) : 0.0;
                $boost = $containsAny($p->page_text ?? '', $keywords) ? 0.25 : 0.0;
                $p->similarity = $sim;
                $p->score = $sim + $boost;
                return $p;
            });

            // Ambil top 50 halaman
            $candidatePages = $scored->sortByDesc('score')->take(50);

            // Sebar per dokumen
            $pagesPerDoc = 2;
            $maxDocs = 8;
            $grouped = $candidatePages->groupBy('document_id')->map(fn($items) => $items->sortByDesc('score')->take($pagesPerDoc)->values());
            $orderedDocs = $grouped->sortByDesc(fn($collection) => $collection->max('score'))->take($maxDocs);

            $selectedPages = collect();
            foreach ($orderedDocs as $collection) {
                foreach ($collection as $p) {
                    $selectedPages->push($p);
                }
            }

            // Jika tetap kosong → tidak ada jawaban
            if ($selectedPages->isEmpty()) {
                $groupChat = $request->group_chat
                    ? GroupChat::where('id', $request->group_chat)->first()
                    : GroupChat::create(['user_id' => auth()->id(), 'title' => $query]);

                Chat::create([
                    'group_chat_id' => $groupChat->id,
                    'is_user' => true,
                    'is_system' => false,
                    'message' => $query
                ]);

                Chat::create([
                    'group_chat_id' => $groupChat->id,
                    'is_system' => true,
                    'is_user' => false,
                    'message' => 'Tidak ada jawaban'
                ]);

                return response()->json([
                    'success' => true,
                    'chat' => 'Tidak ada jawaban',
                    'references' => [],
                    'group_chat_id' => $groupChat->id
                ]);
            }

            // Siapkan context untuk Gemini
            $uniqueDocs = $selectedPages->groupBy('document_id')->map(function ($items) {
                $first = $items->first();
                return [
                    'document_id' => $first->document_id,
                    'title' => $first->title,
                    'file_path' => $first->file_path,
                    'pages' => $items->pluck('page_number')->unique()->values()->all(),
                ];
            })->values();

            $makeSnippet = fn($text, $max = 700) => mb_strlen(trim(preg_replace('/\s+/', ' ', $text)), 'UTF-8') <= $max
                ? trim(preg_replace('/\s+/', ' ', $text))
                : mb_substr(trim(preg_replace('/\s+/', ' ', $text)), 0, $max, 'UTF-8') . ' …';

            $contextBlocks = [];
            foreach ($uniqueDocs as $ud) {
                $pagesOfDoc = $selectedPages->where('document_id', $ud['document_id'])->sortBy('page_number');
                $block = "Dokumen: {$ud['title']}\n";
                foreach ($pagesOfDoc as $p) {
                    $block .= "  Halaman {$p->page_number}:\n    " . $makeSnippet($p->page_text) . "\n";
                }
                $contextBlocks[] = $block;
            }
            $context = implode("\n\n", $contextBlocks);

            // Prompt Gemini: fokus ringkas per dokumen, format output seperti contoh
            $prompt = "Berdasarkan context berikut, buat ringkasan informasi tentang password dan keamanan akun.\n\n"
                . "Context:\n$context\n\n"
                . "Format jawaban YANG WAJIB:\n"
                . "1. Ringkas setiap dokumen dalam 1 paragraf relevan per topik.\n"
                . "2. Tuliskan nama dokumen dan halaman dalam bentuk poin-poin dan bold, contoh:\n"
                . "03.BAB-III-Kebijakan-SMPI.pdf, Halaman 14:\n[Ringkasan informasi password di dokumen ini (berikan ringkasan versi anda)]\n"
                . "3. Hanya tampilkan informasi relevan terkait pertanyaan.\n"
                . "4. Jangan tampilkan potongan halaman mentah.\n"
                . "5. Tulis semua jawaban langsung dalam format html yang support richtext.\n"
                . "6. heading paling besar adalah h3.\n\n"
                . "Jawaban:";

            $rawAnswer = $this->askGemini($prompt) ?: '';
            $answer = $this->formatGeminiResponse($rawAnswer);

            // Siapkan daftar referensi
            $references = $selectedPages->map(fn($p) => [
                'title' => $p->title,
                'file_path' => $p->file_path,
                'page_number' => $p->page_number
            ])->values()->all();

            // Simpan chat
            DB::beginTransaction();
            try {
                $groupChat = $request->group_chat
                    ? GroupChat::where('id', $request->group_chat)->first()
                    : GroupChat::create(['user_id' => auth()->id(), 'title' => $query]);

                Chat::create([
                    'group_chat_id' => $groupChat->id,
                    'is_user' => true,
                    'is_system' => false,
                    'message' => $query
                ]);

                Chat::create([
                    'group_chat_id' => $groupChat->id,
                    'is_system' => true,
                    'is_user' => false,
                    'message' => $answer ?: 'Tidak ada jawaban'
                ]);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'chat' => $answer ?: 'Tidak ada jawaban',
                    'references' => $answer ? $references : [],
                    'group_chat_id' => $groupChat->id
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Error saving chat', ['error' => $e->getMessage()]);

                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menyimpan chat.'
                ]);
            }
        } elseif ($request->type == 'Comply ISO 27001') {
            $request->validate([
                'regulasi' => 'required|mimes:pdf|max:90048',
            ]);

            // Ekstrak teks dari PDF user
            $parser = new \Smalot\PdfParser\Parser();
            $pdf = $parser->parseFile($request->file('regulasi')->getPathname());
            $userText = trim($pdf->getText());

            // Potong teks user agar aman (hanya sebagian diambil untuk analisis awal)
            $maxLength = 2000;
            $userTextChunk = mb_substr($userText, 0, $maxLength);

            // Buat embedding dokumen user
            $embResp = Http::withHeaders(['Content-Type' => 'application/json'])
                ->post(
                    'https://generativelanguage.googleapis.com/v1beta/models/embedding-001:embedContent?key=' . env('GEMINI_API_KEY'),
                    [
                        'model' => 'models/embedding-001',
                        'content' => [
                            'parts' => [
                                ['text' => $userTextChunk]
                            ]
                        ]
                    ]
                );

            $userEmbedding = $embResp->json()['embedding']['values'] ?? [];

            // Ambil semua paragraf ISO yang sudah ada di database
            $isoParagraphs = IsoParagraph::all();
            $scored = [];

            foreach ($isoParagraphs as $para) {
                $paraEmbedding = json_decode($para->embedding, true);
                $score = $this->cosineSimilarityIso($userEmbedding, $paraEmbedding);
                $scored[] = [
                    'paragraph' => $para->paragraph,
                    'page' => $para->page,
                    'score' => $score
                ];
            }

            // Urutkan dari skor terbesar ke terkecil
            usort($scored, fn($a, $b) => $b['score'] <=> $a['score']);

            // Ambil 5 paragraf paling relevan
            $topMatches = array_slice($scored, 0, 5);

            // Buat teks ISO relevan dengan format poin & halaman
            $relevantIso = implode("\n\n", array_map(function ($match, $i) {
                return "Poin " . ($i + 1) . " (Halaman {$match['page']}): {$match['paragraph']}";
            }, $topMatches, array_keys($topMatches)));

            // Prompt ke Gemini agar menyertakan halaman dalam analisis
            $prompt = <<<EOT
Analisis kesesuaian dokumen berikut dengan ISO 27001.

Tampilkan hasil dalam format HTML richtext (tanpa blok kode atau ```).

Struktur jawaban:
1. **Sesuai** — jelaskan poin yang sudah sesuai, sertakan referensi halaman ISO (contoh: "... sesuai dengan ISO 27001 yang dijelaskan di halaman 12") serta buat dalam bentuk poin-poin.
2. **Perlu Perbaikan** — jelaskan poin yang kurang sesuai, sertakan referensi halaman ISO serta buat dalam bentuk poin-poin.
3. **Saran** — berikan rekomendasi perbaikan, tetap sertakan halaman acuan dari ISO serta buat dalam bentuk poin-poin.

Data ISO relevan (nomor poin & halaman):
$relevantIso

Dokumen user:
$userTextChunk
EOT;

            // Kirim ke Gemini untuk analisis
            $response = Http::withHeaders(['Content-Type' => 'application/json'])
                ->post(
                    'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=' . env('GEMINI_API_KEY'),
                    [
                        'contents' => [
                            ['parts' => [['text' => $prompt]]]
                        ]
                    ]
                );

            $hasilJson = $response->json();
            $hasil = $hasilJson['candidates'][0]['content']['parts'][0]['text'] ?? 'Tidak ada jawaban';

            if ($request->group_chat) {
                $groupChat = GroupChat::where('id', $request->group_chat)->first();
            } else {
                $groupChat = GroupChat::create([
                    'user_id' => auth()->id(),
                    'title' => 'Comply ISO 27001'
                ]);
            }
            Chat::create([
                'group_chat_id' => $groupChat->id,
                'is_user' => true,
                'is_system' => false,
                'message' => 'Comply ISO 27001'
            ]);

            Chat::create([
                'group_chat_id' => $groupChat->id,
                'is_system' => true,
                'is_user' => false,
                'message' => $hasil ?: 'Tidak ada jawaban'
            ]);
            DB::commit();

            return response()->json([
                'success' => true,
                'chat' => $hasil
            ]);
        } elseif ($request->type == 'Comply ISO 20000') {
            $answer = "anda memilihj 20000";
            return response()->json([
                'success' => true,
                'chat' => $answer ?: 'Tidak ada jawaban'
            ]);
        }
    }

    private function createEmbedding(string $text)
    {
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post(
            'https://generativelanguage.googleapis.com/v1beta/models/text-embedding-004:embedContent?key=' . env('GEMINI_API_KEY'),
            [
                'model' => 'models/text-embedding-004',
                'content' => [
                    'parts' => [
                        ['text' => $text]
                    ]
                ]
            ]
        );

        $data = $response->json();

        // Cek hasil
        if (isset($data['embedding']['values'])) {
            return $data['embedding']['values'];
        }

        // Debug kalau gagal
        Log::error('Embedding error', $data);
        return null;
    }

    private function cosineSimilarityIso($vecA, $vecB)
    {
        $dotProduct = 0.0;
        $normA = 0.0;
        $normB = 0.0;
        for ($i = 0; $i < count($vecA); $i++) {
            $dotProduct += $vecA[$i] * $vecB[$i];
            $normA += $vecA[$i] ** 2;
            $normB += $vecB[$i] ** 2;
        }
        return $dotProduct / (sqrt($normA) * sqrt($normB));
    }


    // Hitung cosine similarity
    private function cosineSimilarity(array $vec1, array $vec2): float
    {
        $dotProduct = 0;
        $normA = 0;
        $normB = 0;

        foreach ($vec1 as $i => $v) {
            $dotProduct += $v * $vec2[$i];
            $normA += $v ** 2;
            $normB += $vec2[$i] ** 2;
        }

        return $dotProduct / (sqrt($normA) * sqrt($normB));
    }

    // Kirim ke Gemini
    private function askGemini(string $prompt)
    {
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post(
            'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=' . env('GEMINI_API_KEY'),
            [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ]
            ]
        );

        $data = $response->json();
        return $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
    }

    public function getGroupChat(Request $request)
    {
        try {
            $kategori = GroupChat::where('user_id', auth()->id())
                ->with('chats')
                ->orderBy('created_at', 'asc')
                ->get();

            $latestGroupChat = GroupChat::where('user_id', auth()->id())
                ->with('chats')
                ->orderBy('created_at', 'asc')
                ->first();

            return response()->json([
                'status' => true,
                'kategori' => $kategori,
                'latest_group_chat' => $latestGroupChat,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function getChat(Request $request)
    {
        try {
            $groupChat = GroupChat::where('id', $request->group_id)
                ->with('chats')
                ->first();

            if (!$groupChat) {
                return response()->json([
                    'status' => false,
                    'message' => 'Group chat tidak ditemukan.'
                ]);
            }

            return response()->json([
                'status' => true,
                'group_chat' => $groupChat
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function getGroup(Request $request)
    {
        try {
            $groupChat = GroupChat::where('id', $request->group_id)->first();

            if (!$groupChat) {
                return response()->json([
                    'status' => false,
                    'message' => 'Group chat tidak ditemukan.'
                ]);
            }

            return response()->json([
                'status' => true,
                'group_chat' => $groupChat
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function updateGroup(Request $request)
    {
        $request->validate([
            'group_id' => 'required|exists:group_chats,id',
            'title' => 'required|string|max:255',
        ]);

        try {
            $groupChat = GroupChat::findOrFail($request->group_id);
            $groupChat->title = $request->title;
            $groupChat->save();

            return response()->json([
                'status' => true,
                'message' => 'Group chat berhasil diperbarui.',
                'group_chat' => $groupChat
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function deleteGroup(Request $request)
    {
        $request->validate([
            'group_id' => 'required|exists:group_chats,id',
        ]);

        try {
            $groupChat = GroupChat::findOrFail($request->group_id);
            $groupChat->delete();

            return response()->json([
                'status' => true,
                'message' => 'Group chat berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}
