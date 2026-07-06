<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Models\Application;
use App\Models\AIResult;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

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
        $application = Application::with('documents')->find($this->applicationId);

        if (!$application || $application->documents->isEmpty()) {
            Log::error("ScanDocumentJob failed: Application or documents not found for ID {$this->applicationId}");
            return;
        }

        foreach ($application->documents as $document) {
            $fileContents = null;
            $path = $document->file_path;

            if (str_starts_with($path, 'http')) {
                try {
                    $fileContents = file_get_contents($path);
                } catch (\Exception $e) {
                    Log::error("ScanDocumentJob: Failed to download remote document from {$path}: " . $e->getMessage());
                }
            } else {
                if (Storage::disk('local')->exists($path)) {
                    $fileContents = Storage::disk('local')->get($path);
                } else {
                    $pathsToTry = [
                        public_path($path),
                        storage_path('app/' . $path),
                        storage_path('app/public/' . $path),
                        base_path('public/' . $path)
                    ];

                    $actualPath = null;
                    foreach ($pathsToTry as $p) {
                        if ($p && file_exists($p)) {
                            $actualPath = $p;
                            break;
                        }
                    }

                    if ($actualPath) {
                        $fileContents = @file_get_contents($actualPath);
                    }
                }
            }

            if (empty($fileContents)) {
                Log::error("ScanDocumentJob failed: File contents empty or missing from disk for Document ID {$document->id}");
                
                AIResult::updateOrCreate(
                    ['document_id' => $document->id],
                    [
                        'fraud_probability' => 0.00,
                        'classification' => 'failed',
                        'heatmap_path' => null
                    ]
                );
                continue;
            }

            try {
                $aiUrl = config('services.ai.url');
                $response = Http::timeout(120)->attach(
                    'file', $fileContents, $document->original_name
                )->post($aiUrl . '/analyze-document');

                if ($response->successful()) {
                    $result = $response->json();
                    $extractedGwa = $result['extracted_gwa'] ?? null;
                    $fraudProbability = $result['fraud_probability'] ?? 0.00;
                    $classification = $result['classification'] ?? 'Authentic';

                    // GWA Integrity Validation (Logical Fraud Detection)
                    if ($extractedGwa !== null && !empty($application->gwa)) {
                        $declaredGwa = (float) $application->gwa;
                        if (abs($declaredGwa - (float)$extractedGwa) > 0.01) {
                            $fraudProbability = 99.00;
                            $classification = 'Tampered (Grade Discrepancy)';
                            Log::warning("ScanDocumentJob: GWA mismatch detected for Application ID {$application->id}. Declared: {$declaredGwa}, Extracted: {$extractedGwa}");
                        }
                    }

                    AIResult::updateOrCreate(
                        ['document_id' => $document->id],
                        [
                            'fraud_probability' => $fraudProbability,
                            'classification' => $classification,
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
                if (app()->environment('testing')) {
                    throw $e;
                }
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
}
