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

            // 1) Embedding pertanyaan
            $queryEmbedding = $this->createEmbedding($query);
            if (!$queryEmbedding) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal membuat embedding pertanyaan.'
                ]);
            }

            // 2) Ambil semua halaman yang punya embedding
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

            // 3) Siapkan keyword & sinonim untuk boosting
            $qLower = mb_strtolower($query, 'UTF-8');
            $keywords = [$qLower];

            // sinonim sederhana (bisa kamu tambah sesuai domain)
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

            // 4) Skor: cosine similarity + keyword boost
            $scored = $pages->map(function ($p) use ($queryEmbedding, $keywords, $containsAny) {
                $pageEmbedding = json_decode($p->embed, true);
                $sim = $this->cosineSimilarity($queryEmbedding, $pageEmbedding);
                $boost = $containsAny($p->page_text ?? '', $keywords) ? 0.25 : 0.0; // boost jika ada keyword/sinonim
                $p->similarity = $sim;
                $p->score = $sim + $boost; // gabungan
                return $p;
            });

            // 5) Ambil kandidat teratas lebar (untuk cakupan dokumen)
            $candidatePages = $scored->sortByDesc('score')->take(50);

            // 6) Sebar per dokumen: max 2 halaman per dokumen, max 8 dokumen
            $pagesPerDoc = 2;
            $maxDocs = 8;

            $grouped = $candidatePages->groupBy('document_id')->map(function ($items) use ($pagesPerDoc) {
                return $items->sortByDesc('score')->take($pagesPerDoc)->values();
            });

            // Susun kembali mempertahankan urutan dokumen berdasarkan skor tertinggi di masing2 doc
            $orderedDocs = $grouped->sortByDesc(function ($collection) {
                return $collection->max('score');
            })->take($maxDocs);

            $selectedPages = collect();
            foreach ($orderedDocs as $collection) {
                foreach ($collection as $p) {
                    $selectedPages->push($p);
                }
            }

            // 7) Fallback LIKE jika hasil terlalu lemah
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
                    // gunakan penyebaran yang sama per dokumen
                    $groupedLike = $likePages->groupBy('document_id')->map(function ($items) use ($pagesPerDoc) {
                        return $items->take($pagesPerDoc)->values();
                    });

                    $orderedLikeDocs = $groupedLike->sortByDesc(function ($collection) {
                        // kira2 skor 0.5 agar di atas threshold
                        return 0.5;
                    })->take($maxDocs);

                    $selectedPages = collect();
                    foreach ($orderedLikeDocs as $collection) {
                        foreach ($collection as $p) {
                            // isikan nilai default untuk field yang mungkin tidak ada (embed/score)
                            $p->similarity = $p->similarity ?? 0.0;
                            $p->score = $p->score ?? 0.6;
                            $selectedPages->push($p);
                        }
                    }
                }
            }

            // 8) Jika tetap kosong
            if ($selectedPages->isEmpty()) {
                // simpan chat minimal
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

            // 9) Bangun context terstruktur (REF[n]) + snippet (dipotong agar ringkas)
            $docIndex = []; // document_id => REF number
            $refCounter = 1;

            // buat daftar referensi unik by doc untuk kontrol di prompt
            $uniqueDocs = $selectedPages->groupBy('document_id')->map(function ($items) {
                $first = $items->first();
                return [
                    'document_id' => $first->document_id,
                    'title' => $first->title,
                    'file_path' => $first->file_path,
                    'pages' => $items->pluck('page_number')->unique()->values()->all(),
                ];
            })->values();

            foreach ($uniqueDocs as $ud) {
                $docIndex[$ud['document_id']] = $refCounter;
                $refCounter++;
            }

            // potong snippet per halaman
            $makeSnippet = function ($text, $max = 700) {
                $t = trim(preg_replace('/\s+/', ' ', $text));
                if (mb_strlen($t, 'UTF-8') <= $max) return $t;
                return mb_substr($t, 0, $max, 'UTF-8') . ' …';
            };

            $contextBlocks = [];
            foreach ($uniqueDocs as $ud) {
                $refNum = $docIndex[$ud['document_id']];
                $pagesOfDoc = $selectedPages->where('document_id', $ud['document_id'])
                    ->sortBy('page_number');

                $block = "REF[$refNum]: {$ud['title']}\n";
                foreach ($pagesOfDoc as $p) {
                    $block .= "  - Halaman {$p->page_number}: " . $makeSnippet($p->page_text) . "\n";
                }
                $contextBlocks[] = $block;
            }

            $context = implode("\n", $contextBlocks);

            // 10) Prompt Gemini yang memaksa menyebut semua referensi yang relevan
            $prompt = "Gunakan hanya informasi pada daftar referensi berikut untuk menjawab pertanyaan.\n\n"
                . "DAFTAR REFERENSI (gunakan format sitasi REF[n], halaman x):\n$context\n\n"
                . "PERTANYAAN: $query\n\n"
                . "PANDUAN JAWABAN (WAJIB DIIKUTI):\n"
                . "1) Jawab ringkas dan terstruktur dalam HTML <ul><li>...</li></ul> (tanpa ```html```).\n"
                . "2) Setiap butir yang menyebut fakta harus mencantumkan sitasi seperti (REF[n], hlm x).\n"
                . "3) Jika ada lebih dari satu referensi relevan, gabungkan dan sebutkan semuanya pada butir terkait.\n"
                . "4) Jangan gunakan sumber di luar daftar. Jika ada referensi yang tampak relevan tetapi tidak dipakai, tambahkan bagian 'Catatan' yang menjelaskan singkat alasannya.\n"
                . "5) Di akhir, tambahkan bagian <h4>Ringkasan</h4> berisi 2-3 poin utama.\n\n"
                . "Jawaban:";

            $answer = $this->askGemini($prompt) ?: '';

            // 11) Siapkan daftar referensi untuk frontend (tidak bergantung pada apakah disebut di teks)
            //     Kirim satu entry per halaman agar bisa ditautkan tepat sasaran.
            $references = $selectedPages->map(function ($p) {
                return [
                    'title' => $p->title,
                    'file_path' => $p->file_path,
                    'page_number' => $p->page_number
                ];
            })->values()->all();

            // 12) Simpan chat & kembalikan response
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
                    'references' => $answer !== '' ? $references : [],
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
1. **Sesuai** — jelaskan poin yang sudah sesuai, sertakan referensi halaman ISO (contoh: "... sesuai dengan ISO 27001 yang dijelaskan di halaman 12").
2. **Perlu Perbaikan** — jelaskan poin yang kurang sesuai, sertakan referensi halaman ISO.
3. **Saran** — berikan rekomendasi perbaikan, tetap sertakan halaman acuan dari ISO.

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
                ->orderBy('updated_at', 'desc')
                ->get();

            $latestGroupChat = GroupChat::where('user_id', auth()->id())
                ->with('chats')
                ->orderBy('updated_at', 'desc')
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
}
