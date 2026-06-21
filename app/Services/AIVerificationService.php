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

        // 3. Send to Python Flask API (Added a 60-second timeout to allow the CNN to think)
        try {
            $response = Http::timeout(60)->attach(
                'file', file_get_contents($absolutePath), 'cog.jpg'
            )->post('http://127.0.0.1:5000/analyze-document');
        } catch (\Exception $e) {
            throw new \Exception("Connection Error: Could not reach Python API. Make sure 'python app.py' is running in the aegis-ai terminal.");
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