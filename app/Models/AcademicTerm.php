<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicTerm extends Model
{
    use HasFactory;

    protected $fillable = [
        'semester',
        'academic_year',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Relationship: An academic term has many applications.
     */
    public function applications()
    {
        return $this->hasMany(Application::class);
    }

    /**
     * Get the clean formatted semester label (e.g. "2nd Semester" without redundant "Semester").
     */
    public function getFormattedSemesterAttribute(): string
    {
        $val = trim((string) $this->semester);
        if (stripos($val, 'semester') !== false || stripos($val, 'midyear') !== false) {
            return $val;
        }
        return $val . ' Semester';
    }

    /**
     * Get short semester label (e.g. "2nd Sem").
     */
    public function getShortSemesterAttribute(): string
    {
        $val = trim((string) $this->semester);
        return preg_replace('/\bsemester\b/i', 'Sem', $val) ?? $val;
    }

    /**
     * Get full descriptive term string (e.g. "2nd Semester, A.Y. 2025-2026").
     */
    public function getFullTermLabelAttribute(): string
    {
        return $this->formatted_semester . ', A.Y. ' . $this->academic_year;
    }

    /**
     * Cache Busting: Clear active academic term cache on changes.
     */
    protected static function booted()
    {
        static::saved(function ($term) {
            \Illuminate\Support\Facades\Cache::forget('active_academic_term');
        });

        static::deleted(function ($term) {
            \Illuminate\Support\Facades\Cache::forget('active_academic_term');
        });
    }

}
