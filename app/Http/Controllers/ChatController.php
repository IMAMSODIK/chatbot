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

    // === Buat Dictionary Dinamis dari semua dokumen ===
    $allTexts = DB::table('document_pages')->pluck('page_text')->implode(' ');
    $allWords = preg_split('/[\s,.\-:;()]+/', mb_strtolower($allTexts, 'UTF-8'));
    $allWords = array_unique(array_filter($allWords));

    // Tambahkan juga kata penting secara manual
    $extraWords = ['pribadi', 'privasi', 'password', 'sandi', 'credential', 'login', 'pegawai', 'aset'];
    $dictionary = array_unique(array_merge($allWords, $extraWords));

    // === Normalisasi Typo ===
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

        // kalau typo <= 2 huruf → ganti
        if ($shortest > 0 && $shortest <= 2) {
            $normalizedWords[] = $closest;
        } else {
            $normalizedWords[] = $word;
        }
    }

    $query = implode(' ', $normalizedWords);
    // === End tambahan ===

    // 1) Buat embedding pertanyaan
    $queryEmbedding = $this->createEmbedding($query);
    if (!$queryEmbedding) {
        return response()->json([
            'success' => false,
            'message' => 'Gagal membuat embedding pertanyaan.'
        ]);
    }

    // 2) Ambil semua halaman dengan embedding
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

    // 3) Sinonim sederhana untuk boosting
    $qLower = mb_strtolower($query, 'UTF-8');
    $keywords = [$qLower];
    $synonyms = [];

    if (preg_match('/password/i', $query)) {
        $synonyms = ['kata sandi', 'sandi', 'passphrase', 'kredensial', 'credential', 'login', 'autentikasi', 'authentication'];
    }

    $keywords = array_unique(array_merge($keywords, array_map(function ($s) {
        return mb_strtolower($s, 'UTF-8');
    }, $synonyms)));

    $containsAny = function (string $haystack, array $needles): bool {
        $hl = mb_strtolower($haystack, 'UTF-8');
        foreach ($needles as $n) {
            if (mb_strpos($hl, $n) !== false) return true;
        }
        return false;
    };

    // 4) Skoring: cosine similarity + keyword boost
    $scored = $pages->map(function ($p) use ($queryEmbedding, $keywords, $containsAny) {
        $pageEmbedding = json_decode($p->embed, true);
        $sim = $this->cosineSimilarity($queryEmbedding, $pageEmbedding);
        $boost = $containsAny($p->page_text ?? '', $keywords) ? 0.25 : 0.0;
        $p->similarity = $sim;
        $p->score = $sim + $boost;
        return $p;
    });

    // 5) Ambil kandidat teratas lebar
    $candidatePages = $scored->sortByDesc('score')->take(50);

    // 6) Sebar per dokumen
    $pagesPerDoc = 3; // Diperbanyak untuk mendapatkan lebih banyak konteks
    $maxDocs = 5; // Dikurangi untuk fokus pada dokumen paling relevan

    $grouped = $candidatePages->groupBy('document_id')->map(function ($items) use ($pagesPerDoc) {
        return $items->sortByDesc('score')->take($pagesPerDoc)->values();
    });

    $orderedDocs = $grouped->sortByDesc(function ($collection) {
        return $collection->max('score');
    })->take($maxDocs);

    $selectedPages = collect();
    foreach ($orderedDocs as $collection) {
        foreach ($collection as $p) {
            $selectedPages->push($p);
        }
    }

    // 7) Fallback LIKE jika hasil lemah
    $bestScore = $selectedPages->max('score');
    if ($selectedPages->isEmpty() || $bestScore < 0.2) {
        $likePages = DB::table('document_pages')
            ->join('documents', 'document_pages.document_id', '=', 'documents.id')
            ->where('document_pages.page_text', 'LIKE', "%{$query}%")
            ->select(
                'document_pages.id',
                'document_pages.document_id',
                'document_pages.page_number',
                'document_pages.page_text',
                'documents.title',
                'documents.file_path'
            )
            ->take(20)
            ->get();

        if ($likePages->isNotEmpty()) {
            $groupedLike = $likePages->groupBy('document_id')->map(function ($items) use ($pagesPerDoc) {
                return $items->take($pagesPerDoc)->values();
            });

            $orderedLikeDocs = $groupedLike->sortByDesc(function () {
                return 0.5;
            })->take($maxDocs);

            $selectedPages = collect();
            foreach ($orderedLikeDocs as $collection) {
                foreach ($collection as $p) {
                    $p->similarity = $p->similarity ?? 0.0;
                    $p->score = $p->score ?? 0.6;
                    $selectedPages->push($p);
                }
            }
        }
    }

    // 8) Jika tetap kosong → tidak ada jawaban
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

    // 9) Bangun context per dokumen + halaman
    $uniqueDocs = $selectedPages->groupBy('document_id')->map(function ($items) {
        $first = $items->first();
        return [
            'document_id' => $first->document_id,
            'title' => $first->title,
            'file_path' => $first->file_path,
            'pages' => $items->pluck('page_number')->unique()->values()->all(),
        ];
    })->values();

    // 10) Prompt Gemini yang lebih spesifik
    $prompt = "Anda adalah asisten yang membantu menjawab pertanyaan berdasarkan dokumen yang diberikan.\n\n"
        . "INFORMASI DOKUMEN:\n"
        . "Berikut adalah dokumen-dokumen yang relevan dengan pertanyaan:\n\n";

    foreach ($uniqueDocs as $doc) {
        $docPages = $selectedPages->where('document_id', $doc['document_id'])
            ->sortBy('page_number');
        
        $prompt .= "DOKUMEN: {$doc['title']}\n";
        $prompt .= "HALAMAN TERKAIT: " . implode(', ', $doc['pages']) . "\n";
        $prompt .= "ISI:\n";
        
        foreach ($docPages as $page) {
            $cleanText = trim(preg_replace('/\s+/', ' ', $page->page_text));
            $prompt .= "Halaman {$page->page_number}: {$cleanText}\n";
        }
        $prompt .= "\n";
    }

    $prompt .= "PERTANYAAN: {$query}\n\n"
        . "INSTRUKSI JAWABAN:\n"
        . "1. Jawab dengan jelas dan lengkap berdasarkan informasi dari dokumen di atas\n"
        . "2. Kelompokkan jawaban berdasarkan dokumen sumber\n"
        . "3. Untuk setiap dokumen, sebutkan nama file dan halaman yang relevan\n"
        . "4. Gunakan format: [Nama File.pdf], Halaman [X]: [Penjelasan singkat tentang informasi password dalam dokumen ini]\n"
        . "5. Jangan membuat informasi yang tidak ada dalam dokumen\n"
        . "6. Jika ada kebijakan spesifik tentang password, jelaskan dengan detail\n"
        . "7. Gunakan bahasa Indonesia yang formal dan jelas\n\n"
        . "JAWABAN:";

    $rawAnswer = $this->askGemini($prompt) ?: '';

    // 11) Format ulang jawaban untuk konsistensi
    $answer = $this->formatGeminiResponse($rawAnswer);

    // 12) Siapkan daftar referensi (hanya dokumen yang benar-benar mengandung informasi relevan)
    $relevantReferences = $selectedPages->filter(function ($p) use ($query) {
        // Filter hanya halaman yang benar-benar mengandung kata kunci
        $textLower = mb_strtolower($p->page_text, 'UTF-8');
        $queryLower = mb_strtolower($query, 'UTF-8');
        return mb_strpos($textLower, $queryLower) !== false || 
               (preg_match('/password/i', $query) && preg_match('/password|sandi|kredensial/i', $textLower));
    })->map(function ($p) {
        return [
            'title' => $p->title,
            'file_path' => $p->file_path,
            'page_number' => $p->page_number
        ];
    })->values()->all();

    // 13) Simpan chat & balikan response
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
            'message' => $answer !== '' ? $answer : 'Tidak ada jawaban'
        ]);

        DB::commit();

        return response()->json([
            'success' => true,
            'chat' => $answer !== '' ? $answer : 'Tidak ada jawaban',
            'references' => $answer !== '' ? $relevantReferences : [],
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
