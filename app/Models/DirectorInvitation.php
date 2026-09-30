<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DirectorInvitation extends Model
{
    protected $fillable = [
        'invited_by_email',
        'recipient_email',
        'recipient_name',
        'token',
        'expires_at',
        'accepted_at',
        'accepted_by_user_id',
    ];

    protected $casts = [
        'expires_at'  => 'datetime',
        'accepted_at' => 'datetime',
    ];

    /** The user who accepted this invitation. */
    public function acceptedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'accepted_by_user_id');
    }

    /** Whether the invitation is still valid and pending. */
    public function isPending(): bool
    {
        return is_null($this->accepted_at) && $this->expires_at->isFuture();
    }

    /** Whether the invitation has been accepted. */
    public function isAccepted(): bool
    {
        return !is_null($this->accepted_at);
    }

    /** Whether the invitation has expired without being accepted. */
    public function isExpired(): bool
    {
        return is_null($this->accepted_at) && $this->expires_at->isPast();
    }
}
