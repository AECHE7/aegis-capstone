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
     * @param  int|null  $assignedTo         The assigned_to staff ID on the Application record
     */
    private function authorizeDocumentAccess(?int $applicationUserId, ?int $scholarshipId, ?int $assignedTo = null): void
    {
        $user = auth()->user();

        if (!$user) {
            abort(401, 'Unauthenticated.');
        }

        if ($user->role === 'student') {
            if ($applicationUserId !== $user->id) {
                abort(403, 'Unauthorized access.');
            }
            return;
        }

        if ($user->role === 'superadmin' || (method_exists($user, 'isMaster') && $user->isMaster())) {
            return;
        }

        if ($user->role === 'admin') {
            // If the application is explicitly assigned to this staff member, always permit access
            if ($assignedTo !== null && $assignedTo === $user->id) {
                return;
            }

            // If the staff member has restricted scholarship scopes, enforce matching scholarship
            $assignedIds = $user->scholarships()->pluck('scholarships.id')->toArray();
            if (!empty($assignedIds) && !in_array($scholarshipId, $assignedIds)) {
                abort(403, 'Unauthorized access.');
            }

            // Unconstrained staff evaluators can review all incoming/active scholarship documents
            return;
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
            $document->application->scholarship_id ?? null,
            $document->application->assigned_to ?? null
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
                return response($binary, 200, array_merge([
                    'Content-Type'        => $mime,
                    'Content-Disposition' => 'inline; filename="' . ($document->original_name ?? 'document.' . $ext) . '"'
                ], $this->sensitiveCacheHeaders()));
            }
            return $this->localFileMissingResponse('This local file was wiped from server memory during redeployment. Please re-upload or contact support.');
        }

        return response()->file(Storage::disk('local')->path($path), $this->sensitiveCacheHeaders());
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
            $aiResult->document->application->scholarship_id ?? null,
            $aiResult->document->application->assigned_to ?? null
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
            $aiResult->document->application->scholarship_id ?? null,
            $aiResult->document->application->assigned_to ?? null
        );

        $deepReport = $aiResult->deep_analysis_report;
        if (is_string($deepReport)) {
            $deepReport = json_decode($deepReport, true) ?? [];
        }
        $originalPageBase64 = $deepReport['visualizations']['original_page_base64'] ?? null;

        if (!empty($originalPageBase64)) {
            $binary = base64_decode($originalPageBase64);
            return response($binary, 200, array_merge([
                'Content-Type'        => 'image/png',
                'Content-Disposition' => 'inline; filename="original_page_' . $id . '.png"',
            ], $this->sensitiveCacheHeaders()));
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
            $aiResult->document->application->scholarship_id ?? null,
            $aiResult->document->application->assigned_to ?? null
        );

        $deepReport = $aiResult->deep_analysis_report;
        if (is_string($deepReport)) {
            $deepReport = json_decode($deepReport, true) ?? [];
        }

        $layers = $deepReport['visualizations']['layer_heatmaps'] ?? [];
        $layerBase64 = $layers[$layer] ?? null;

        if (!empty($layerBase64)) {
            $binary = base64_decode($layerBase64);
            return response($binary, 200, array_merge([
                'Content-Type'        => 'image/png',
                'Content-Disposition' => 'inline; filename="layer_' . $layer . '_' . $id . '.png"',
            ], $this->sensitiveCacheHeaders()));
        }

        // Graceful Fallback: stream standard heatmap or original view instead of returning 404!
        if (!empty($aiResult->heatmap_data)) {
            $binary = base64_decode($aiResult->heatmap_data);
            return response($binary, 200, array_merge([
                'Content-Type'        => 'image/jpeg',
                'Content-Disposition' => 'inline; filename="heatmap_' . $id . '.jpg"'
            ], $this->sensitiveCacheHeaders()));
        }

        return $this->view($id);
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
            $field->application->scholarship_id ?? null,
            $field->application->assigned_to ?? null
        );

        $path = $field->field_value;

        if (str_starts_with($path, 'http')) {
            return $this->proxyRemoteFile($path, 'Remote Stream Failed', 'Could not stream the custom field document from Cloudflare R2 bucket. Please check connection.');
        }

        if (!Storage::disk('local')->exists($path)) {
            // Check if document table retains base64 database backup for this upload
            $doc = \App\Models\Document::where('file_path', $path)
                ->orWhere(function ($q) use ($field) {
                    $q->where('application_id', $field->application_id)
                      ->where('document_type', $field->field_name);
                })
                ->first();

            if ($doc && !empty($doc->file_data)) {
                $binary = base64_decode($doc->file_data);
                $ext = strtolower(pathinfo($doc->original_name ?? $path, PATHINFO_EXTENSION)) ?: 'pdf';
                $mime = match($ext) {
                    'pdf' => 'application/pdf',
                    'png' => 'image/png',
                    'jpg', 'jpeg' => 'image/jpeg',
                    default => 'application/octet-stream'
                };
                return response($binary, 200, array_merge([
                    'Content-Type'        => $mime,
                    'Content-Disposition' => 'inline; filename="' . basename($path) . '"',
                ], $this->sensitiveCacheHeaders()));
            }

            return $this->localFileMissingResponse('This local file was wiped from server memory during redeployment. Please configure Cloudflare R2 bucket settings.');
        }

        return response()->file(Storage::disk('local')->path($path), $this->sensitiveCacheHeaders());
    }

    /**
     * Proxy a remote (R2/CDN) file through the server response with SSRF defenses.
     */
    private function proxyRemoteFile(string $url, string $title, string $description)
    {
        $parsed = parse_url($url);
        $scheme = strtolower($parsed['scheme'] ?? '');
        $host   = strtolower($parsed['host'] ?? '');

        // 1. Enforce HTTPS in production/staging; block dangerous schemes
        if (!in_array($scheme, ['https', 'http'], true)) {
            return $this->remoteStreamFailedResponse($title, 'Invalid URI scheme.');
        }

        if ($scheme !== 'https' && !app()->environment(['local', 'testing'])) {
            return $this->remoteStreamFailedResponse($title, 'Insecure remote connection blocked.');
        }

        // 2. SSRF Protection: Block internal IP addresses and cloud metadata services (169.254.169.254, 127.0.0.1, RFC1918)
        if (filter_var($host, FILTER_VALIDATE_IP)) {
            if (!filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                if (!app()->environment(['local', 'testing'])) {
                    Log::warning("SSRF block: Attempt to proxy private/reserved IP: {$host}");
                    return $this->remoteStreamFailedResponse($title, 'Access to private IP endpoints is restricted.');
                }
            }
        }

        // 3. Block loopback and link-local hostnames in production
        if (!app()->environment(['local', 'testing'])) {
            if (in_array($host, ['localhost', '127.0.0.1', '::1', 'metadata.google.internal', '169.254.169.254'], true)) {
                return $this->remoteStreamFailedResponse($title, 'Access to local or metadata hostnames is forbidden.');
            }
        }

        try {
            $response = Http::timeout(15)->get($url);
            if ($response->successful()) {
                $mime = $response->header('Content-Type') ?: 'application/octet-stream';
                return response($response->body(), 200, array_merge([
                    'Content-Type'        => $mime,
                    'Content-Disposition' => 'inline; filename="' . basename($url) . '"',
                ], $this->sensitiveCacheHeaders()));
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

    /**
     * Privacy & security cache control headers for sensitive student documents.
     */
    private function sensitiveCacheHeaders(): array
    {
        return [
            'Cache-Control' => 'private, no-cache, no-store, must-revalidate',
            'Pragma'        => 'no-cache',
            'Expires'       => '0',
        ];
    }
}