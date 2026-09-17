<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use App\Models\Document;
use App\Models\AIResult;

class AIVerificationService
{
    public function analyzeDocument(Document $document)
    {
        // 1. Check if the file exists using the correct disk
        $disk = Storage::disk('local');
        
        if (!$disk->exists($document->file_path)) {
            throw new \Exception("Storage Error: File path exists in DB but not on disk (" . $document->file_path . ")");
        }

        // 2. Get the absolute system path
        $absolutePath = $disk->path($document->file_path);

        // 3. Send to Python Flask API with auto-wake retry loop for sleeping containers
        $aiUrl = rtrim(config('services.ai.url', 'http://127.0.0.1:5000'), '/');
        $response = null;
        $maxAttempts = 3;

        for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
            try {
                $response = Http::timeout(120)->attach(
                    'file', file_get_contents($absolutePath), 'cog.jpg'
                )->post($aiUrl . '/analyze-document');

                if ($response->successful()) {
                    break;
                }

                // If container is cold/sleeping (502/503/504), send wake-up probe and wait
                if (in_array($response->status(), [502, 503, 504]) && $attempt < $maxAttempts) {
                    \Illuminate\Support\Facades\Log::warning("AIVerificationService: AI service returned HTTP {$response->status()} (cold start). Attempt {$attempt}/{$maxAttempts}. Warming up...");
                    try { @Http::timeout(15)->get($aiUrl . '/health'); } catch (\Throwable $t) {}
                    sleep(8 * $attempt);
                    continue;
                }
            } catch (\Exception $e) {
                if ($attempt < $maxAttempts) {
                    \Illuminate\Support\Facades\Log::warning("AIVerificationService: Connection error on attempt {$attempt} ({$e->getMessage()}). Pinging health check to wake container...");
                    try { @Http::timeout(15)->get($aiUrl . '/health'); } catch (\Throwable $t) {}
                    sleep(8 * $attempt);
                    continue;
                }
                throw new \Exception("Connection Error: Could not reach Python API at " . $aiUrl . ". Details: " . $e->getMessage());
            }
        }

        // 4. Handle the Response
        if ($response->successful()) {
            $data = $response->json();
            
            // Check if Python returned a specific error
            if (isset($data['error'])) {
                throw new \Exception("Python API Error: " . $data['error']);
            }

            // Save results
            return AIResult::updateOrCreate(
                ['document_id' => $document->id],
                [
                    'fraud_probability' => $data['fraud_probability'],
                    'classification' => $data['classification'],
                    'heatmap_path' => $data['paths']['heatmap_path'],
                ]
            );
        }

        // If the server returned a 500 error
        throw new \Exception("Microservice Crash: HTTP " . $response->status() . " - " . $response->body());
    }
}