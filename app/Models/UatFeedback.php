<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UatFeedback extends Model
{
    use HasFactory;

    protected $table = 'uat_feedbacks';

    protected $fillable = [
        'user_id',
        'role',
        'functional_suitability',
        'usability',
        'reliability',
        'security',
        'comments'
    ];

    /**
     * Relationship: Feedback belongs to a user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
