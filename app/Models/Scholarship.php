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
        'max_renewals'
    ];

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