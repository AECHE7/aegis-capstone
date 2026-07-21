<?php

namespace App\Services;

use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\Log;

class ImageExifInspector
{
    protected static array $editingTools = [
        'photoshop', 'gimp', 'canva', 'photopea', 'lightroom', 
        'paint.net', 'adobe', 'coreldraw', 'picasa', 'pixlr',
        'snapseed', 'vsco', 'fotor', 'affinity'
    ];

    /**
     * Inspects image EXIF metadata for software manipulation signatures and metadata anomalies.
     *
     * @param string $filePath Absolute local file path to the image
     * @return array Structure containing risk_score, indicators, software, camera
     */
    public static function inspect(string $filePath): array
    {
        $indicators = [];
        $riskScore = 0.0;
        $software = null;
        $camera = null;

        if (!file_exists($filePath)) {
            return [
                'risk_score' => 0.0,
                'indicators' => [],
                'software' => null,
                'camera' => null
            ];
        }

        // 1. Try ExifTool CLI process execution if binary is present
        $exifData = self::readExifViaTool($filePath);

        // 2. Fallback to native PHP exif_read_data if ExifTool binary is not installed
        if (empty($exifData) && function_exists('exif_read_data')) {
            $exifData = self::readExifViaPhp($filePath);
        }

        if (empty($exifData)) {
            // Un-segmented or stripped image metadata
            return [
                'risk_score' => $riskScore,
                'indicators' => $indicators,
                'software' => null,
                'camera' => null
            ];
        }

        // Extract Software / Creator Tool tags
        $softwareTag = strtolower($exifData['Software'] ?? $exifData['ProcessingSoftware'] ?? $exifData['CreatorTool'] ?? '');
        if (!empty($softwareTag)) {
            $software = $exifData['Software'] ?? $exifData['ProcessingSoftware'] ?? $exifData['CreatorTool'];
            foreach (self::$editingTools as $tool) {
                if (str_contains($softwareTag, $tool)) {
                    $indicators[] = 'exif_software_editing_tool';
                    $riskScore += 35.0;
                    Log::warning("ImageExifInspector: Suspicious software tag '{$software}' detected in file {$filePath}");
                    break;
                }
            }
        }

        // Extract Camera / Device tags
        $make = $exifData['Make'] ?? '';
        $model = $exifData['Model'] ?? '';
        if ($make || $model) {
            $camera = trim("{$make} {$model}");
        }

        // Check for CreationDate vs DateTimeOriginal vs ModifyDate discrepancy (> 1 day gap)
        $dateTimeOriginal = $exifData['DateTimeOriginal'] ?? $exifData['CreateDate'] ?? null;
        $modifyDate = $exifData['ModifyDate'] ?? $exifData['DateTime'] ?? null;

        if ($dateTimeOriginal && $modifyDate && $dateTimeOriginal !== $modifyDate) {
            try {
                $tOrig = strtotime($dateTimeOriginal);
                $tMod = strtotime($modifyDate);
                if ($tOrig && $tMod && abs($tMod - $tOrig) > 86400) {
                    $indicators[] = 'exif_modify_date_mismatch';
                    $riskScore += 15.0;
                }
            } catch (\Throwable $e) {
                // Ignore date parsing exceptions
            }
        }

        return [
            'risk_score' => min(100.0, $riskScore),
            'indicators' => array_values(array_unique($indicators)),
            'software' => $software,
            'camera' => $camera,
        ];
    }

    /**
     * Reads EXIF metadata using system exiftool binary.
     */
    protected static function readExifViaTool(string $filePath): array
    {
        try {
            $process = new Process(['exiftool', '-j', $filePath]);
            $process->setTimeout(5);
            $process->run();

            if ($process->isSuccessful()) {
                $output = json_decode($process->getOutput(), true);
                if (is_array($output) && count($output) > 0) {
                    return $output[0];
                }
            }
        } catch (\Throwable $e) {
            // ExifTool binary not installed or failed to execute
        }

        return [];
    }

    /**
     * Reads EXIF metadata using native PHP function.
     */
    protected static function readExifViaPhp(string $filePath): array
    {
        try {
            $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg', 'jpeg', 'tif', 'tiff'])) {
                $data = @exif_read_data($filePath);
                return is_array($data) ? $data : [];
            }
        } catch (\Throwable $e) {
            // Failed to parse EXIF via PHP
        }

        return [];
    }
}
