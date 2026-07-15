<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Application extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id', 
        'scholarship_id', 
        'academic_term_id',
        'program_name', 
        'gwa', 
        'status',
        'remarks',
        'admin_notes',
        'evaluated_by',
        'assigned_to',
        'is_archived',
        'is_renewal',
        'previous_application_id',
        'forfeit_reason',
    ];

    protected $casts = [
        'is_archived' => 'boolean',
        'is_renewal'  => 'boolean',
        'assigned_to' => 'integer',
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
     * Relationship: An application has many uploaded documents (COG + Custom Uploads)
     */
    public function documents()
    {
        return $this->hasMany(Document::class);
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

    /**
     * Identifies the staff member assigned to review this application
     */
    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Relationship: An application has many custom form fields (responses)
     */
    public function customFields()
    {
        return $this->hasMany(ApplicationField::class);
    }

    /**
     * Invalidate analytics cache when applications are saved, deleted, or restored.
     */
    protected static function booted()
    {
        $invalidateCache = function () {
            try {
                $version = \Illuminate\Support\Facades\Cache::get('analytics_cache_version', 1);
                \Illuminate\Support\Facades\Cache::put('analytics_cache_version', $version + 1, 2592000);
            } catch (\Exception $e) {
                // Fail-safe
            }
        };

        static::saved($invalidateCache);
        static::deleted($invalidateCache);
        static::restored($invalidateCache);
    }
}