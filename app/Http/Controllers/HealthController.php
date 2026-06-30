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
        } catch (\Exception $e) {
            $dbStatus = 'failed';
            $dbError = $e->getMessage();
        }

        $storageStatus = 'ok';
        $storageError = null;

        try {
            Storage::disk('local')->exists('health-check-temp');
        } catch (\Exception $e) {
            $storageStatus = 'failed';
            $storageError = $e->getMessage();
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
