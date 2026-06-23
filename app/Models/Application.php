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
        'academic_term_id',
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
     * Relationship: An application belongs to a scholarship.
     */
    public function scholarship()
    {
        return $this->belongsTo(Scholarship::class);
    }

    /**
     * Relationship: An application belongs to an academic term.
     */
    public function academicTerm()
    {
        return $this->belongsTo(AcademicTerm::class);
    }

    /**
     * Relationship: An application has many status logs.
     */
    public function statusLogs()
    {
        return $this->hasMany(StatusLog::class);
    }

    /**
     * Relationship: An application has many email notification logs.
     */
    public function emailLogs()
    {
        return $this->hasMany(EmailLog::class);
    }

    /**
     * THE MISSING LINK: Identifies the Admin who evaluated this application
     */
    public function evaluator()
    {
        return $this->belongsTo(User::class, 'evaluated_by');
    }
}