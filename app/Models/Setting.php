<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    private static array $runtimeCache = [];

    /**
     * Retrieve a setting value by key, with caching.
     */
    public static function get(string $key, $default = null)
    {
        if (array_key_exists($key, self::$runtimeCache)) {
            return self::$runtimeCache[$key];
        }

        try {
            $val = Cache::rememberForever("setting.{$key}", function () use ($key, $default) {
                $setting = self::where('key', $key)->first();
                return $setting ? $setting->value : $default;
            });
            self::$runtimeCache[$key] = $val;
            return $val;
        } catch (\Throwable $e) {
            return $default;
        }
    }

    /**
     * Set a setting value by key, flushing cache.
     */
    public static function set(string $key, $value): self
    {
        $setting = self::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );

        unset(self::$runtimeCache[$key]);
        Cache::forget("setting.{$key}");

        return $setting;
    }

    /**
     * Clear all static runtime caches for settings.
     */
    public static function clearRuntimeCache(): void
    {
        self::$runtimeCache = [];
    }

    /**
     * Get system logo URL (uploaded system logo if available and exists, or default CLSU logo asset).
     */
    public static function getLogoUrl(): string
    {
        $customLogo = self::get('app_logo');
        if ($customLogo) {
            if (str_starts_with($customLogo, 'http') || str_starts_with($customLogo, 'data:')) {
                return $customLogo;
            }
            if (\Illuminate\Support\Facades\Storage::disk('local')->exists($customLogo)) {
                $url = route('system.logo');
                return str_replace('http://', 'https://', $url);
            }
        }

        // Reliable fallback to CLSU seal
        if (file_exists(public_path('images/clsu-seal.png'))) {
            return asset('images/clsu-seal.png');
        }

        if (file_exists(public_path('logo.png'))) {
            return asset('logo.png');
        }

        if (file_exists(public_path('logo.webp'))) {
            return asset('logo.webp');
        }

        $domain = config('app.url', 'https://clsu.osa.scholarship');
        if (!$domain || str_contains($domain, 'localhost')) {
            if (request()->hasHeader('X-Forwarded-Host')) {
                $proto = request()->header('X-Forwarded-Proto', 'https');
                $host = request()->header('X-Forwarded-Host');
                $domain = "{$proto}://{$host}";
            } elseif (request()->getHost() && !str_contains(request()->getHost(), 'localhost')) {
                $domain = request()->schemeAndHttpHost();
            } else {
                $domain = 'https://clsu.osa.scholarship';
            }
        }
        return rtrim(str_replace('http://', 'https://', $domain), '/') . '/images/clsu-seal.png';
    }
}
