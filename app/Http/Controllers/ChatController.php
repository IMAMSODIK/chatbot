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

            // 1️⃣ Buat embedding untuk pertanyaan
            $queryEmbedding = $this->createEmbedding($query);

            if (!$queryEmbedding) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal membuat embedding pertanyaan.'
                ]);
            }

            // 2️⃣ Ambil document_pages + join ke documents
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

            // 3️⃣ Hitung cosine similarity
            $scoredPages = $pages->map(function ($page) use ($queryEmbedding) {
                $pageEmbedding = json_decode($page->embed, true);
                $similarity = $this->cosineSimilarity($queryEmbedding, $pageEmbedding);
                $page->similarity = $similarity;
                return $page;
            });

            // 4️⃣ Ambil top 5 halaman
            $topPages = $scoredPages->sortByDesc('similarity')->take(5);

            if ($topPages->isEmpty()) {
                if($request->group_chat) {
                    $groupChat = GroupChat::where('id', $request->group_chat)->first();
                } else {
                    $groupChat = GroupChat::create([
                        'user_id' => auth()->id(),
                        'title' => $query
                    ]);
                }
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
                DB::commit();

                return response()->json([
                    'success' => true,
                    'chat' => 'Tidak ada jawaban',
                    'references' => [],
                    'group_chat_id' => $groupChat->id
                ]);
            }

            // 5️⃣ Gabungkan context
            $context = "";
            foreach ($topPages as $p) {
                $context .= "Dokumen: {$p->title}, Halaman: {$p->page_number}\n";
                $context .= "{$p->page_text}\n\n";
            }

            // 6️⃣ Prompt ke Gemini
            $prompt = "Gunakan informasi berikut untuk menjawab pertanyaan.\n\n"
                . "Context:\n$context\n\n"
                . "Pertanyaan: $query\n\n"
                . "Format jawaban: Sebutkan dokumen dan halaman yang relevan, misal menurut dokumen A halaman xx dijelaskan... juga berikan jawaban dalam format HTML karena saya ingin menampilkannya di richtext, dan tanpa ada ```html```.\n"
                . "Jawaban:";

            $answer = $this->askGemini($prompt);

            // 7️⃣ Filter referensi hanya yang muncul di jawaban
            $mentionedDocs = [];
            foreach ($topPages as $p) {
                if (stripos($answer, $p->title) !== false) {
                    $mentionedDocs[] = [
                        'title' => $p->title,
                        'file_path' => $p->file_path,
                        'page_number' => $p->page_number
                    ];
                }
            }

            DB::beginTransaction();
            try {
                if($request->group_chat) {
                    $groupChat = GroupChat::where('id', $request->group_chat)->first();
                } else {
                    $groupChat = GroupChat::create([
                        'user_id' => auth()->id(),
                        'title' => $query
                    ]);
                }
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
                    'message' => $answer
                ]);
                DB::commit();

                return response()->json([
                    'success' => true,
                    'chat' => $answer ?: 'Tidak ada jawaban',
                    'references' => $answer ? $mentionedDocs : [],
                    'group_chat_id' => $groupChat->id
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Error saving chat', ['error' => $e->getMessage()]);
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

            if($request->group_chat) {
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
