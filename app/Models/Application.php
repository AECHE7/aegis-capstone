<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    use HasFactory;

    // We make sure the audit trail columns are allowed to be saved
    protected $fillable = [
        'user_id', 
        'scholarship_id', 
        'program_name', 
        'gwa', 
        'status',
        'remarks',
        'evaluated_by'
    ];

    /**
     * Relationship: An application belongs to a student (User)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship: An application has one uploaded document
     */
    public function document()
    {
        return $this->hasOne(Document::class);
    }

    /**
     * THE MISSING LINK: Identifies the Admin who evaluated this application
     */
    public function evaluator()
    {
        return $this->belongsTo(User::class, 'evaluated_by');
    }
}