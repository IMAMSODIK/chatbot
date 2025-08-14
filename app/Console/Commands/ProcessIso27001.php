<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Smalot\PdfParser\Parser;
use App\Models\IsoParagraph;

class ProcessIso27001 extends Command
{
    protected $signature = 'process:iso27001';
    protected $description = 'Pecah ISO 27001 PDF jadi paragraf per halaman dan simpan embedding ke database';

    public function handle()
    {
        $pdfPath = Storage::disk('public')->path('iso/iso_27001.pdf');

        if (!file_exists($pdfPath)) {
            $this->error("❌ File ISO 27001 PDF tidak ditemukan di: {$pdfPath}");
            return;
        }

        $this->info("📄 Memproses file: {$pdfPath}");

        $parser = new Parser();
        $pdf = $parser->parseFile($pdfPath);
        $pages = $pdf->getPages();

        foreach ($pages as $pageNumber => $page) {
            $this->info("➡ Memproses halaman " . ($pageNumber + 1));

            $text = trim($page->getText());

            // Pecah teks menjadi paragraf dengan deteksi baris kosong
            $paragraphs = preg_split("/\n\s*\n/", $text);

            foreach ($paragraphs as $para) {
                $para = trim(preg_replace('/\s+/', ' ', $para)); // rapikan spasi
                if (strlen($para) < 20) {
                    continue; // skip jika terlalu pendek
                }

                // Buat embedding dengan Gemini
                $response = Http::withHeaders([
                        'Content-Type' => 'application/json'
                    ])
                    ->post(
                        'https://generativelanguage.googleapis.com/v1beta/models/embedding-001:embedContent?key=' . env('GEMINI_API_KEY'),
                        [
                            'model' => 'models/embedding-001',
                            'content' => [
                                'parts' => [
                                    ['text' => $para]
                                ]
                            ]
                        ]
                    );

                $embedding = $response->json()['embedding']['values'] ?? null;

                if ($embedding) {
                    IsoParagraph::create([
                        'paragraph'   => $para,
                        'page' => $pageNumber + 1, // Simpan nomor halaman
                        'embedding'   => json_encode($embedding)
                    ]);
                }
            }
        }

        $this->info("✅ ISO 27001 PDF berhasil diproses dan disimpan ke database dengan nomor halaman.");
    }
}
