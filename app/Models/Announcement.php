<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'author_id',
        'scheduled_publish_at',
        'scheduled_delete_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'scheduled_publish_at' => 'datetime',
            'scheduled_delete_at' => 'datetime',
        ];
    }

    /**
     * Relationship: An announcement is authored by a user (admin or superadmin)
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}
