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
        'author_id'
    ];

    /**
     * Relationship: An announcement is authored by a user (admin or superadmin)
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}
