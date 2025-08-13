<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\DocumentPage;
use App\Models\Documents;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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
            return response()->json([
                'success' => true,
                'chat' => 'Tidak ada jawaban',
                'references' => []
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
            . "Format jawaban: Sebutkan dokumen dan halaman yang relevan, misal menurut dokumen A halaman xx dijelaskan... juga berikan jawaban dalam format html karena saya ingin menampilkannya di richtext, dan tanpa ada ```html```.\n"
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

        return response()->json([
            'success' => true,
            'chat' => $answer ?: 'Tidak ada jawaban',
            'references' => $answer ? $mentionedDocs : []
        ]);
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
}
