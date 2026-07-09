<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuthLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'email_attempted',
        'event_type',
        'ip_address',
        'user_agent',
        'status',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    /** The user this event belongs to (nullable for failed logins). */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
