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

    public $tries = 5;

    public function backoff(): array
    {
        return [15, 45, 90, 180, 360];
    }

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

            if (empty($fileContents) && !empty($document->file_data)) {
                $fileContents = base64_decode($document->file_data);
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
                    $fraudProbability = (float) ($result['fraud_probability'] ?? 0.00);

                    // Determine classification dynamically based on db threshold setting
                    $thresholdSetting = (float) \App\Models\Setting::get('ai_fraud_threshold', 50.0);
                    $classification = $fraudProbability >= $thresholdSetting ? 'tampered' : ($result['classification'] ?? 'Authentic');

                    // GWA Integrity Validation (Logical Fraud Detection)
                    if ($extractedGwa !== null && !empty($application->gwa)) {
                        $declaredGwa = (float) $application->gwa;
                        $gwaTolerance = (float) \App\Models\Setting::get('gwa_discrepancy_tolerance', 0.01);
                        if (abs($declaredGwa - (float)$extractedGwa) > $gwaTolerance) {
                            $fraudProbability = 99.00;
                            $classification = 'Tampered (Grade Discrepancy)';
                            Log::warning("ScanDocumentJob: GWA mismatch detected for Application ID {$application->id}. Declared: {$declaredGwa}, Extracted: {$extractedGwa}");
                        }
                    }

                    $anomalyIndicators = $result['anomaly_indicators'] ?? [];

                    // Run native EXIF metadata inspection for image uploads
                    $ext = strtolower(pathinfo($document->original_name, PATHINFO_EXTENSION));
                    if (in_array($ext, ['jpg', 'jpeg', 'png', 'tif', 'tiff']) && !empty($actualPath)) {
                        $exifRes = \App\Services\ImageExifInspector::inspect($actualPath);
                        if (!empty($exifRes['indicators'])) {
                            $anomalyIndicators = array_values(array_unique(array_merge($anomalyIndicators, $exifRes['indicators'])));
                        }
                        if ($exifRes['risk_score'] > 0) {
                            $fraudProbability = min(100.0, max($fraudProbability, $exifRes['risk_score']));
                        }
                    }

                    AIResult::updateOrCreate(
                        ['document_id' => $document->id],
                        [
                            'fraud_probability' => $fraudProbability,
                            'classification' => $classification,
                            'heatmap_path' => $result['paths']['heatmap_path'] ?? null,
                            'anomaly_indicators' => $anomalyIndicators,
                        ]
                    );
                } else {
                    Log::error("ScanDocumentJob API error: " . $response->body());
                    throw new \Exception("AI Service returned HTTP " . $response->status() . ": " . $response->body());
                }
            } catch (\Exception $e) {
                Log::error("ScanDocumentJob exception: " . $e->getMessage());
                throw $e;
            }
        }

        // Evaluate smart auto-approval after scanning completes
        \App\Services\ApplicationAutoApprovalService::evaluate($application);
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        $application = Application::with('documents')->find($this->applicationId);
        if ($application) {
            foreach ($application->documents as $document) {
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
