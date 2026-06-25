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

    /**
     * Relationship: An academic term has many applications.
     */
    public function applications()
    {
        return $this->hasMany(Application::class);
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
