<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use App\Models\IsoParagraph;

class ProcessIso27001 extends Command
{
    protected $signature = 'process:iso27001';
    protected $description = 'Pecah ISO 27001 jadi paragraf dan simpan embedding ke database';

    public function handle()
    {
        $text = Storage::disk('public')->get('iso/iso_27001.txt');

        $paragraphs = preg_split("/\n\s*\n/", $text);

        foreach ($paragraphs as $para) {
            $para = trim($para);
            if (strlen($para) < 20) continue;

            // Buat embedding pakai Gemini
            $response = Http::withHeaders(['Content-Type' => 'application/json'])
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
                    'paragraph' => $para,
                    'embedding' => json_encode($embedding)
                ]);
            }
        }

        $this->info("ISO 27001 berhasil diproses dan disimpan.");
    }
}
