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
    public function __construct(public int $applicationId, public string $mode = 'standard')
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
                    $actualPath = Storage::disk('local')->path($path);
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
                $aiUrl = rtrim(config('services.ai.url', 'http://127.0.0.1:5000'), '/');
                $response = null;
                $maxAttempts = 3;

                for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
                    try {
                        $response = Http::timeout(120)->attach(
                            'file', $fileContents, $document->original_name
                        )->post($aiUrl . '/analyze-document?mode=' . urlencode($this->mode));

                        if ($response->successful()) {
                            break;
                        }

                        // If 503 / 502 / 504 (Service Unavailable / Cold Start), ping health check and retry
                        if (in_array($response->status(), [502, 503, 504]) && $attempt < $maxAttempts) {
                            Log::warning("ScanDocumentJob: AI service returned HTTP {$response->status()} (cold start). Attempt {$attempt} of {$maxAttempts}. Waiting for container to warm up...");
                            try { @Http::timeout(10)->get($aiUrl . '/health'); } catch (\Throwable $t) {}
                            sleep(5 * $attempt);
                            continue;
                        }
                    } catch (\Exception $reqEx) {
                        if ($attempt < $maxAttempts) {
                            Log::warning("ScanDocumentJob connection error on attempt {$attempt}: " . $reqEx->getMessage() . ". Retrying...");
                            try { @Http::timeout(10)->get($aiUrl . '/health'); } catch (\Throwable $t) {}
                            sleep(5);
                            continue;
                        }
                        throw $reqEx;
                    }
                }

                if (!$response || !$response->successful()) {
                    $status = $response ? $response->status() : 503;
                    $body = $response ? $response->body() : 'Service Unavailable';
                    if ($status === 503) {
                        throw new \Exception("AI Service is currently warming up from sleep on free tier (HTTP 503). Please wait 30-45 seconds for container initialization and retry.");
                    }
                    throw new \Exception("AI Service returned HTTP {$status}: {$body}");
                }

                $result = $response->json();
                $extractedGwa = $result['extracted_gwa'] ?? null;
                $fraudProbability = (float) ($result['fraud_probability'] ?? 0.00);

                // Determine classification dynamically based on db threshold setting
                $thresholdSetting = (float) \App\Models\Setting::get('ai_fraud_threshold', 70.0);
                $classification = $fraudProbability >= $thresholdSetting ? 'tampered' : ($result['classification'] ?? 'Authentic');

                // GWA Integrity Validation — 4-Tier Justifiable Discrepancy Engine
                // -----------------------------------------------------------------------
                // Replacing the previous blunt 99.00% penalty on any OCR variance.
                // Tesseract OCR on mobile photos frequently misreads single digits (e.g.
                // 1.75 → 1.76) or reads a per-semester GWA when the student declared a
                // cumulative GWA. A proportional tiered engine is academically defensible.
                // -----------------------------------------------------------------------
                $anomalyIndicators = []; // Initialized here; merged with AI indicators below
                if ($extractedGwa !== null && !empty($application->gwa)) {
                    $declaredGwa    = (float) $application->gwa;
                    $extractedGwaF  = (float) $extractedGwa;
                    $gwaTolerance   = (float) \App\Models\Setting::get('gwa_discrepancy_tolerance', 0.01);
                    $diff           = abs($declaredGwa - $extractedGwaF);

                    if ($diff <= $gwaTolerance) {
                        // Tier 1 — Match: GWA verified. No penalty.
                        Log::info("ScanDocumentJob: GWA verified for Application ID {$application->id}. Declared: {$declaredGwa}, Extracted: {$extractedGwaF}");

                    } elseif ($diff <= 0.05) {
                        // Tier 2 — Minor OCR Variance (≤ 0.05): Likely a Tesseract digit
                        // misread or format rounding. Flag for human eye review — do NOT
                        // criminally brand the student. Moderate advisory penalty only.
                        $fraudProbability = min(100.0, $fraudProbability + 20.0);
                        if ($fraudProbability < 35.0) { $fraudProbability = 35.0; } // Ensure Review Needed range
                        $classification   = 'Review Needed (Minor Grade Variance)';
                        $anomalyIndicators[] = "gwa_minor_variance:declared_{$declaredGwa}_vs_extracted_{$extractedGwaF}";
                        Log::warning("ScanDocumentJob: Minor GWA variance (diff={$diff}) for Application ID {$application->id}. Declared: {$declaredGwa}, Extracted: {$extractedGwaF}. Flagged for review.");

                    } elseif ($declaredGwa < $extractedGwaF) {
                        // Tier 3 — Grade Inflation: In the Philippine grading system,
                        // lower numbers = better grades (1.0 = excellent, 5.0 = failing).
                        // Student declared a LOWER (better) numeric grade than what OCR
                        // extracted from the transcript. This is a strong fraud signal.
                        $fraudProbability = 99.00;
                        $classification   = 'Tampered (Grade Discrepancy)';
                        $anomalyIndicators[] = "gwa_discrepancy:declared_{$declaredGwa}_vs_extracted_{$extractedGwaF}";
                        Log::warning("ScanDocumentJob: Grade inflation discrepancy (diff={$diff}, PH-scale) for Application ID {$application->id}. Declared: {$declaredGwa}, Extracted: {$extractedGwaF}.");

                    } else {
                        // Tier 4 — Inverse Variance: Student declared a HIGHER (worse)
                        // numeric grade than transcript shows. Likely a data-entry mistake,
                        // not fraud — no student would intentionally claim a worse grade.
                        // Moderate review flag — not a criminal accusation.
                        $fraudProbability = min(100.0, max(45.0, $fraudProbability));
                        $classification   = 'Review Needed (Grade Input Variance)';
                        $anomalyIndicators[] = "gwa_input_variance:declared_{$declaredGwa}_vs_extracted_{$extractedGwaF}";
                        Log::info("ScanDocumentJob: Inverse GWA variance (diff={$diff}, PH-scale) for Application ID {$application->id}. Declared: {$declaredGwa}, Extracted: {$extractedGwaF}.");
                    }
                }

                // Merge AI-provided anomaly indicators with GWA-derived indicators.
                // IMPORTANT: Do NOT overwrite with $result['anomaly_indicators'] directly — this
                // would destroy the GWA discrepancy indicators we just appended above.
                $aiAnomalyIndicators = $result['anomaly_indicators'] ?? [];
                $anomalyIndicators = array_values(array_unique(array_merge($aiAnomalyIndicators, $anomalyIndicators)));
                $detectedSoftware = $result['detected_software'] ?? null;

                // Run native EXIF metadata inspection for image uploads
                $ext = strtolower(pathinfo($document->original_name, PATHINFO_EXTENSION));
                if (in_array($ext, ['jpg', 'jpeg', 'png', 'tif', 'tiff']) && !empty($actualPath)) {
                    $exifRes = \App\Services\ImageExifInspector::inspect($actualPath);
                    if (!empty($exifRes['indicators'])) {
                        $anomalyIndicators = array_values(array_unique(array_merge($anomalyIndicators, $exifRes['indicators'])));
                    }
                    // EXIF metadata risk contributes proportionally as Pillar 4 (Metadata Provenance — 15% weight).
                    // Using bounded additive scoring instead of a destructive max() override that previously
                    // caused innocent gallery-cropped mobile photos to dominate the visual fusion score.
                    if ($exifRes['risk_score'] > 0) {
                        $exifContribution = $exifRes['risk_score'] * 0.15; // 15% Pillar 4 weight
                        $fraudProbability = min(100.0, $fraudProbability + $exifContribution);
                    }
                    if (!empty($exifRes['software']) && empty($detectedSoftware)) {
                        $detectedSoftware = $exifRes['software'];
                    }
                }

                $deepReport = $result['deep_analysis_report'] ?? null;
                if ($deepReport && isset($result['visualizations'])) {
                    $deepReport['visualizations'] = $result['visualizations'];
                }

                // Always store extracted_gwa in deep_analysis_report so the Evaluator Review
                // Studio can display exact declared vs. extracted values in the OCR pillar badge.
                if ($extractedGwa !== null) {
                    if ($deepReport === null) { $deepReport = []; }
                    if (!isset($deepReport['extracted_gwa'])) {
                        $deepReport['extracted_gwa'] = (float) $extractedGwa;
                    }
                }

                AIResult::updateOrCreate(
                    ['document_id' => $document->id],
                    [
                        'fraud_probability'  => $fraudProbability,
                        'classification'     => $classification,
                        'heatmap_path'       => $result['paths']['heatmap_path'] ?? null,
                        'heatmap_data'       => $result['heatmap_base64'] ?? null,
                        'anomaly_indicators' => $anomalyIndicators,
                        'detected_software'  => $detectedSoftware,
                        'cropped_patch_data' => $result['cropped_patch_base64'] ?? null,
                        'deep_analysis_report' => $deepReport,
                    ]
                );
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
