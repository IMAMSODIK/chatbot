<?php

namespace App\Http\Controllers;

use App\Models\DocumentPage;
use App\Models\Documents;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Smalot\PdfParser\Parser;

class DocumentsController extends Controller
{
    public function index()
    {
        $data = [
            'pageTitle' => "Dokumen",
            'data' => Documents::all()
        ];
        return view('dokumen.index', $data);
    }

    public function delete(Request $r)
    {
        $validatedData = $r->validate([
            'id' => 'required|string',
        ], [
            'id.required' => 'Data belum dipilih.',
            'id.string' => 'Data belum dipilih.',
        ]);

        try {
            $dokumen = Documents::where('id', $r->id)->first();

            if ($dokumen) {
                if ($dokumen->path && Storage::exists($dokumen->path)) {
                    Storage::delete($dokumen->path);
                }

                $dokumen->delete();

                return response()->json([
                    'status' => true,
                    'data' => $dokumen
                ]);
            }

            return response()->json([
                'status' => false,
                'message' => "Data tidak ditemukan"
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function store(Request $request)
    {
        set_time_limit(300);
        $request->validate([
            'files.*' => 'required|mimes:pdf|max:20480',
        ]);

        $parser = new Parser();
        $results = [];

        foreach ($request->file('files') as $file) {
            $filePath = $file->store('documents', 'public');
            $fileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

            $pdf = $parser->parseFile(storage_path("app/public/{$filePath}"));
            $pages = $pdf->getPages();

            $document = Documents::create([
                'title' => $fileName,
                'file_path' => $filePath,
                'file_type' => 'pdf',
                'extracted_text' => '',
                'uploaded_by' => auth()->id(),
                'embed' => null
            ]);

            $fullText = '';

            foreach ($pages as $index => $page) {
                $pageText = trim($page->getText());
                if (!$pageText) continue;

                $fullText .= $pageText . "\n";
                $embeddingVector = $this->createEmbedding($pageText);

                DocumentPage::create([
                    'document_id' => $document->id,
                    'page_number' => $index + 1,
                    'page_text' => $pageText,
                    'embed' => $embeddingVector ? json_encode($embeddingVector) : null
                ]);
            }

            $document->update(
                [
                    'extracted_text' => $fullText,
                    'embed' => $this->createEmbedding($fullText) ? json_encode($this->createEmbedding($fullText)) : null
                ]
            );

            $results[] = $document;
        }

        return response()->json([
            'status' => true,
            'data' => $results
        ]);
    }

    private function createEmbedding($text, $retries = 3)
    {
        $text = substr($text, 0, 3000);

        for ($i = 0; $i < $retries; $i++) {
            $res = Http::post(
                'https://generativelanguage.googleapis.com/v1beta/models/embedding-001:embedContent?key=' . env('GEMINI_API_KEY'),
                [
                    'model' => 'models/embedding-001',
                    'content' => [
                        'parts' => [
                            ['text' => $text]
                        ]
                    ]
                ]
            );

            $vector = $res->json()['embedding']['values'] ?? null;
            if ($vector) return $vector;

            sleep(1);
        }

        return null;
    }
}
