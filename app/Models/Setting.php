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
     * Get system logo URL (uploaded system logo if available, or default CLSU logo asset).
     */
    public static function getLogoUrl(): string
    {
        $customLogo = self::get('app_logo');
        if ($customLogo) {
            $url = route('system.logo');
            return str_replace('http://', 'https://', $url);
        }

        $domain = config('app.url', 'https://aegis-capstone.onrender.com');
        if (!$domain || str_contains($domain, 'localhost')) {
            if (request()->hasHeader('X-Forwarded-Host')) {
                $proto = request()->header('X-Forwarded-Proto', 'https');
                $host = request()->header('X-Forwarded-Host');
                $domain = "{$proto}://{$host}";
            } elseif (request()->getHost() && !str_contains(request()->getHost(), 'localhost')) {
                $domain = request()->schemeAndHttpHost();
            } else {
                $domain = 'https://aegis-capstone.onrender.com';
            }
        }
        return rtrim(str_replace('http://', 'https://', $domain), '/') . '/logo-email.png';
    }
}
