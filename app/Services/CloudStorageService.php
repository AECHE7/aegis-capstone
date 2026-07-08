<?php

declare(strict_types=1);

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

    /**
     * Delete a file from R2 (if URL) or local disk.
     *
     * @param string $filePath  Full URL for R2 files, relative path for local files.
     * @return bool
     */
    public static function delete(string $filePath): bool
    {
        if (empty($filePath)) {
            return false;
        }

        if (str_starts_with($filePath, 'http')) {
            // Extract the object key from the R2 URL
            $hasR2 = !empty(config('filesystems.disks.r2.key'))
                  && !empty(config('filesystems.disks.r2.secret'))
                  && !empty(config('filesystems.disks.r2.bucket'));

            if (!$hasR2) {
                return false;
            }

            try {
                $baseUrl = config('filesystems.disks.r2.url') ?? '';
                // Derive the relative key from the full URL
                $key = ltrim(str_replace(rtrim($baseUrl, '/'), '', $filePath), '/');
                Storage::disk('r2')->delete($key);
                return true;
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('CloudStorageService::delete R2 failed: ' . $e->getMessage());
                return false;
            }
        }

        // Local file
        if (Storage::disk('local')->exists($filePath)) {
            Storage::disk('local')->delete($filePath);
            return true;
        }

        return false;
    }
}
