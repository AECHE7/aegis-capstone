<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    /**
     * Retrieve a setting value by key, with caching.
     */
    public static function get(string $key, $default = null)
    {
        try {
            return Cache::rememberForever("setting.{$key}", function () use ($key, $default) {
                $setting = self::where('key', $key)->first();
                return $setting ? $setting->value : $default;
            });
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
            return route('system.logo');
        }
        return asset('logo.webp');
    }
}
