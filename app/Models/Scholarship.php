<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Scholarship extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'min_gwa_required',
        'deadline',
        'status',
        'max_renewals',
        'quota',
    ];

    protected $casts = [
        'quota' => 'integer',
        'max_renewals' => 'integer',
        'min_gwa_required' => 'float',
        'deadline' => 'date',
    ];

    /**
     * Get the count of approved scholars for this program.
     */
    public function approvedCount(): int
    {
        return $this->applications()->where('status', 'Approved')->count();
    }

    /**
     * Get remaining available slots (or null if unlimited).
     */
    public function availableSlots(): ?int
    {
        if ($this->quota === null) {
            return null;
        }
        return max(0, $this->quota - $this->approvedCount());
    }

    /**
     * Check if program quota has reached or exceeded 100% capacity.
     */
    public function isQuotaExhausted(): bool
    {
        return $this->quota !== null && $this->approvedCount() >= $this->quota;
    }

    /**
     * Calculate slot utilization percentage.
     */
    public function quotaUtilizationPct(): float
    {
        if (!$this->quota || $this->quota <= 0) {
            return 0.0;
        }
        return round(min(100, ($this->approvedCount() / $this->quota) * 100), 1);
    }

    public function applications()
    {
        return $this->hasMany(Application::class);
    }

    /**
     * Relationship: A scholarship has many custom fields configured
     */
    public function fields()
    {
        return $this->hasMany(ScholarshipField::class);
    }

    /**
     * Relationship: A scholarship belongs to many assigned staff (many-to-many)
     */
    public function staff()
    {
        return $this->belongsToMany(User::class, 'scholarship_staff');
    }

    /**
     * Cache Busting: Clear active scholarships list cache on changes.
     */
    protected static function booted()
    {
        static::saved(function ($scholarship) {
            \Illuminate\Support\Facades\Cache::forget('active_scholarships_list');
        });

        static::deleted(function ($scholarship) {
            \Illuminate\Support\Facades\Cache::forget('active_scholarships_list');
        });

        static::restored(function ($scholarship) {
            \Illuminate\Support\Facades\Cache::forget('active_scholarships_list');
        });
    }
}