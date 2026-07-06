<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CloudStorageService
{
    /**
     * Upload an uploaded file to R2 if configured, or fall back to local disk.
     *
     * @param UploadedFile $file
     * @param string $folder
     * @return string The public URL or local storage path
     */
    public static function upload(UploadedFile $file, string $folder = 'uploads'): string
    {
        $hasR2 = !empty(env('CLOUDFLARE_R2_ACCESS_KEY_ID')) 
              && !empty(env('CLOUDFLARE_R2_SECRET_ACCESS_KEY')) 
              && !empty(env('CLOUDFLARE_R2_BUCKET'));

        $extension = $file->getClientOriginalExtension();
        $uuid = (string) Str::uuid();
        $filename = hash('sha256', $uuid) . '.' . $extension;

        if ($hasR2) {
            try {
                // Upload to Cloudflare R2
                $path = Storage::disk('r2')->putFileAs($folder, $file, $filename);
                if ($path) {
                    $baseUrl = env('CLOUDFLARE_R2_URL');
                    if (empty($baseUrl)) {
                        // fallback to constructing the R2 public endpoint
                        $endpoint = env('CLOUDFLARE_R2_ENDPOINT');
                        $baseUrl = rtrim($endpoint, '/') . '/' . env('CLOUDFLARE_R2_BUCKET');
                    }
                    return rtrim($baseUrl, '/') . '/' . $folder . '/' . $filename;
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("CloudStorageService upload to R2 failed: " . $e->getMessage() . ". Falling back to local storage.");
            }
        }

        // Fallback to local storage (standard local disk)
        $path = $file->storeAs($folder, $filename, 'local');
        return $path ?: $folder . '/' . $filename;
    }
}
