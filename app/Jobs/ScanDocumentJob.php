<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Models\Application;
use App\Models\AIResult;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ScanDocumentJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public int $applicationId)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $application = Application::with('document')->find($this->applicationId);

        if (!$application || !$application->document) {
            Log::error("ScanDocumentJob failed: Application or document not found for ID {$this->applicationId}");
            return;
        }

        $document = $application->document;

        // Locate file path on disk
        $pathsToTry = [
            public_path($document->file_path),
            storage_path('app/public/' . $document->file_path),
            base_path('public/' . $document->file_path)
        ];

        $actualPath = null;
        foreach ($pathsToTry as $path) {
            if ($path && file_exists($path)) {
                $actualPath = $path;
                break;
            }
        }

        if (!$actualPath) {
            Log::error("ScanDocumentJob failed: File missing from disk for Document ID {$document->id}");
            
            AIResult::updateOrCreate(
                ['document_id' => $document->id],
                [
                    'fraud_probability' => 0.00,
                    'classification' => 'failed',
                    'heatmap_path' => null
                ]
            );
            return;
        }

        try {
            $aiUrl = config('services.ai.url');
            $response = Http::timeout(120)->attach(
                'file', file_get_contents($actualPath), $document->original_name
            )->post($aiUrl . '/analyze-document');

            if ($response->successful()) {
                $result = $response->json();

                AIResult::updateOrCreate(
                    ['document_id' => $document->id],
                    [
                        'fraud_probability' => $result['fraud_probability'] ?? 0.00,
                        'classification' => $result['classification'] ?? 'Authentic',
                        'heatmap_path' => $result['paths']['heatmap_path'] ?? null,
                    ]
                );
            } else {
                Log::error("ScanDocumentJob API error: " . $response->body());
                AIResult::updateOrCreate(
                    ['document_id' => $document->id],
                    [
                        'fraud_probability' => 0.00,
                        'classification' => 'failed',
                        'heatmap_path' => null
                    ]
                );
            }
        } catch (\Exception $e) {
            Log::error("ScanDocumentJob exception: " . $e->getMessage());
            AIResult::updateOrCreate(
                ['document_id' => $document->id],
                [
                    'fraud_probability' => 0.00,
                    'classification' => 'failed',
                    'heatmap_path' => null
                ]
            );
        }
    }
}
