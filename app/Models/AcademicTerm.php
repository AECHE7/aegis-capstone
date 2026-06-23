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
}
