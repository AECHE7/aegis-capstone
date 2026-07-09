<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConfigChangeLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'setting_key',
        'old_value',
        'new_value',
        'ip_address',
    ];

    /** The superadmin who changed the setting. */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
