<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserMfaDevice extends Model
{
    protected $fillable = [
        'user_id',
        'device_token',
        'ip_address',
        'user_agent_hash',
        'user_agent',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getHumanReadableAttribute(): string
    {
        $ua = $this->user_agent ?: 'Unknown Device';
        
        $os = 'Unknown OS';
        if (preg_match('/windows|win32/i', $ua)) { $os = 'Windows'; }
        elseif (preg_match('/macintosh|mac os x/i', $ua)) { $os = 'macOS'; }
        elseif (preg_match('/iphone|ipad/i', $ua)) { $os = 'iOS'; }
        elseif (preg_match('/android/i', $ua)) { $os = 'Android'; }
        elseif (preg_match('/linux/i', $ua)) { $os = 'Linux'; }

        $browser = 'Unknown Browser';
        if (preg_match('/chrome|crios/i', $ua) && !preg_match('/opr/i', $ua) && !preg_match('/edge/i', $ua)) { $browser = 'Chrome'; }
        elseif (preg_match('/safari/i', $ua) && !preg_match('/chrome/i', $ua)) { $browser = 'Safari'; }
        elseif (preg_match('/firefox|fxios/i', $ua)) { $browser = 'Firefox'; }
        elseif (preg_match('/edge|edg/i', $ua)) { $browser = 'Edge'; }
        elseif (preg_match('/opera|opr/i', $ua)) { $browser = 'Opera'; }

        if ($browser === 'Unknown Browser' && $os === 'Unknown OS') {
            return substr($ua, 0, 50) . (strlen($ua) > 50 ? '...' : '');
        }

        return "{$browser} on {$os}";
    }
}
