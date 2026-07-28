<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * DocumentController
 *
 * Handles secure file viewing for document images, AI heatmaps,
 * and custom application field file uploads.
 *
 * Extracted from route closures in web.php (HIGH-01).
 * Authorization is enforced per user role in authorizeDocumentAccess().
 */
class DocumentController extends Controller
{
    /**
     * Enforce role-based access to a resource linked to an application.
     *
     * @param  int|null  $applicationUserId  The user_id on the Application record
     * @param  int|null  $scholarshipId      The scholarship_id on the Application record
     */
    private function authorizeDocumentAccess(?int $applicationUserId, ?int $scholarshipId): void
    {
        $user = auth()->user();

        if ($user->role === 'student' && $applicationUserId !== auth()->id()) {
            abort(403, 'Unauthorized access.');
        }

        if ($user->role === 'admin') {
            $assignedIds = $user->scholarships()->pluck('scholarships.id')->toArray();
            if (!in_array($scholarshipId, $assignedIds)) {
                abort(403, 'Unauthorized access.');
            }
        }
    }

    /**
     * Stream or proxy the raw document file (COG image / PDF).
     * Route: GET /document/{id}/image
     */
    public function view(int $id)
    {
        $document = \App\Models\Document::with('application')->findOrFail($id);

        $this->authorizeDocumentAccess(
            $document->application->user_id ?? null,
            $document->application->scholarship_id ?? null
        );

        $path = $document->file_path;

        if (str_starts_with($path, 'http')) {
            return $this->proxyRemoteFile($path, 'Remote Stream Failed', 'Could not stream the document from Cloudflare R2 bucket. Please check connection.');
        }

        if (!Storage::disk('local')->exists($path)) {
            if (!empty($document->file_data)) {
                $binary = base64_decode($document->file_data);
                $ext = strtolower(pathinfo($document->original_name ?? 'file.pdf', PATHINFO_EXTENSION));
                $mime = match($ext) {
                    'pdf' => 'application/pdf',
                    'png' => 'image/png',
                    'jpg', 'jpeg' => 'image/jpeg',
                    default => 'application/octet-stream'
                };
                return response($binary, 200, [
                    'Content-Type'        => $mime,
                    'Content-Disposition' => 'inline; filename="' . ($document->original_name ?? 'document.' . $ext) . '"'
                ]);
            }
            return $this->localFileMissingResponse('This local file was wiped from server memory during redeployment. Please re-upload or contact support.');
        }

        return response()->file(Storage::disk('local')->path($path));
    }

    /**
     * Redirect to the AI-generated heatmap image.
     * Route: GET /document/{id}/heatmap
     */
    public function heatmap(int $id)
    {
        $aiResult = \App\Models\AIResult::with('document.application')->where('document_id', $id)->firstOrFail();

        $this->authorizeDocumentAccess(
            $aiResult->document->application->user_id ?? null,
            $aiResult->document->application->scholarship_id ?? null
        );

        // 1. Database Persistence Check: Stream base64 heatmap directly from PostgreSQL if present
        if (!empty($aiResult->heatmap_data)) {
            $binary = base64_decode($aiResult->heatmap_data);
            return response($binary, 200, [
                'Content-Type'        => 'image/jpeg',
                'Content-Disposition' => 'inline; filename="heatmap_' . $id . '.jpg"'
            ]);
        }

        $path = $aiResult->heatmap_path;

        if (!empty($path)) {
            if (str_starts_with($path, 'http')) {
                try {
                    $res = \Illuminate\Support\Facades\Http::timeout(10)->get($path);
                    if ($res->successful()) {
                        return response($res->body(), 200, [
                            'Content-Type'        => $res->header('Content-Type') ?? 'image/jpeg',
                            'Content-Disposition' => 'inline; filename="heatmap_' . $id . '.jpg"'
                        ]);
                    }
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::warning("Remote heatmap stream failed for URL {$path}: " . $e->getMessage());
                }
            }

            $aiUrl = rtrim(config('services.ai.url'), '/');
            $fullHeatmapUrl = $aiUrl . '/heatmap/' . basename($path);
            try {
                $res = \Illuminate\Support\Facades\Http::timeout(10)->get($fullHeatmapUrl);
                if ($res->successful()) {
                    return response($res->body(), 200, [
                        'Content-Type'        => $res->header('Content-Type') ?? 'image/jpeg',
                        'Content-Disposition' => 'inline; filename="heatmap_' . $id . '.jpg"'
                    ]);
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning("AI microservice heatmap stream failed for URL {$fullHeatmapUrl}: " . $e->getMessage());
            }
        }

        // 2. Graceful Fallback: Stream original document file instead of throwing 500 error!
        return $this->view($id);
    }

    /**
     * Stream the rasterized original page preview for a PDF or image.
     * Route: GET /document/{id}/original-page
     */
    public function originalPage(int $id)
    {
        $aiResult = \App\Models\AIResult::with('document.application')->where('document_id', $id)->firstOrFail();

        $this->authorizeDocumentAccess(
            $aiResult->document->application->user_id ?? null,
            $aiResult->document->application->scholarship_id ?? null
        );

        $deepReport = $aiResult->deep_analysis_report;
        $originalPageBase64 = $deepReport['visualizations']['original_page_base64'] ?? null;

        if (!empty($originalPageBase64)) {
            $binary = base64_decode($originalPageBase64);
            return response($binary, 200, [
                'Content-Type'        => 'image/png',
                'Content-Disposition' => 'inline; filename="original_page_' . $id . '.png"',
                'Cache-Control'       => 'public, max-age=31536000, immutable'
            ]);
        }

        // Fall back to showing the original raw file (e.g. if it is an image document)
        return $this->view($id);
    }

    /**
     * Stream a specific forensic layer (e.g. ela_detailed, noise_consistency, edge_consistency).
     * Route: GET /document/{id}/forensic-layer/{layer}
     */
    public function forensicLayer(int $id, string $layer)
    {
        $aiResult = \App\Models\AIResult::with('document.application')->where('document_id', $id)->firstOrFail();

        $this->authorizeDocumentAccess(
            $aiResult->document->application->user_id ?? null,
            $aiResult->document->application->scholarship_id ?? null
        );

        $deepReport = $aiResult->deep_analysis_report;
        $layers = $deepReport['visualizations']['layer_heatmaps'] ?? [];
        
        $layerBase64 = $layers[$layer] ?? null;

        if (!empty($layerBase64)) {
            $binary = base64_decode($layerBase64);
            return response($binary, 200, [
                'Content-Type'        => 'image/png',
                'Content-Disposition' => 'inline; filename="layer_' . $layer . '_' . $id . '.png"',
                'Cache-Control'       => 'public, max-age=31536000, immutable'
            ]);
        }

        abort(404, 'Forensic layer not found.');
    }

    /**
     * Stream or proxy a custom application field file upload.
     * Route: GET /application-field/{id}/file
     */
    public function fieldFile(int $id)
    {
        $field = \App\Models\ApplicationField::with('application')->findOrFail($id);

        $this->authorizeDocumentAccess(
            $field->application->user_id ?? null,
            $field->application->scholarship_id ?? null
        );

        $path = $field->field_value;

        if (str_starts_with($path, 'http')) {
            return $this->proxyRemoteFile($path, 'Remote Stream Failed', 'Could not stream the custom field document from Cloudflare R2 bucket. Please check connection.');
        }

        if (!Storage::disk('local')->exists($path)) {
            return $this->localFileMissingResponse('This local file was wiped from server memory during redeployment. Please configure Cloudflare R2 bucket settings.');
        }

        return response()->file(Storage::disk('local')->path($path));
    }

    /**
     * Proxy a remote (R2/CDN) file through the server response.
     */
    private function proxyRemoteFile(string $url, string $title, string $description)
    {
        try {
            $response = Http::timeout(15)->get($url);
            if ($response->successful()) {
                $mime = $response->header('Content-Type') ?: 'application/octet-stream';
                return response($response->body(), 200, [
                    'Content-Type'        => $mime,
                    'Content-Disposition' => 'inline; filename="' . basename($url) . '"',
                ]);
            }
        } catch (\Exception $e) {
            Log::error("DocumentController: Failed to stream remote document [{$url}]: " . $e->getMessage());
        }

        return $this->remoteStreamFailedResponse($title, $description);
    }

    /**
     * Return a styled HTML error response for a failed remote stream.
     * Returns HTTP 503 (Service Unavailable) — not 200 (MED-07 fix).
     */
    private function remoteStreamFailedResponse(string $title, string $description)
    {
        return response()->view('errors.document_stream_failed', compact('title', 'description'), 503);
    }

    /**
     * Return a styled HTML error response when a local file is missing.
     * Returns HTTP 404 (MED-07 fix).
     */
    private function localFileMissingResponse(string $message)
    {
        return response()->view('errors.document_missing', compact('message'), 404);
    }
}