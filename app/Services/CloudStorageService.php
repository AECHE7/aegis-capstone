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
        $hasR2 = !empty(config('filesystems.disks.r2.key')) 
              && !empty(config('filesystems.disks.r2.secret')) 
              && !empty(config('filesystems.disks.r2.bucket'));

        $extension = $file->getClientOriginalExtension();
        $uuid = (string) Str::uuid();
        $filename = hash('sha256', $uuid) . '.' . $extension;

        if ($hasR2) {
            try {
                // Upload to Cloudflare R2
                $path = Storage::disk('r2')->putFileAs($folder, $file, $filename);
                if ($path) {
                    $baseUrl = config('filesystems.disks.r2.url');
                    if (empty($baseUrl)) {
                        // fallback to constructing the R2 public endpoint
                        $endpoint = config('filesystems.disks.r2.endpoint');
                        $baseUrl = rtrim($endpoint, '/') . '/' . config('filesystems.disks.r2.bucket');
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
