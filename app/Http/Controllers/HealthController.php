<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class HealthController extends Controller
{
    /**
     * Perform service health checks.
     *
     * @return JsonResponse
     */
    public function check(): JsonResponse
    {
        $dbStatus = 'ok';
        $dbError = null;

        try {
            DB::connection()->getPdo();
        } catch (\Throwable $e) {
            $dbStatus = 'failed';
            \Illuminate\Support\Facades\Log::error('Health check database probe failed: ' . $e->getMessage());
            $dbError = config('app.debug') ? $e->getMessage() : 'Database connection unavailable.';
        }

        $storageStatus = 'ok';
        $storageError = null;

        try {
            Storage::disk('local')->exists('health-check-temp');
        } catch (\Throwable $e) {
            $storageStatus = 'failed';
            \Illuminate\Support\Facades\Log::error('Health check storage probe failed: ' . $e->getMessage());
            $storageError = config('app.debug') ? $e->getMessage() : 'Storage disk unavailable.';
        }

        $healthy = ($dbStatus === 'ok' && $storageStatus === 'ok');

        return response()->json([
            'status' => $healthy ? 'ok' : 'error',
            'timestamp' => now()->toIso8601String(),
            'environment' => config('app.env'),
            'checks' => [
                'database' => [
                    'status' => $dbStatus,
                    'error' => $dbError,
                ],
                'storage' => [
                    'status' => $storageStatus,
                    'error' => $storageError,
                ],
            ],
        ], $healthy ? 200 : 500);
    }
}
