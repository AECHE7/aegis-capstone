<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Scholarship extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'min_gwa_required',
        'deadline',
        'status'
    ];

    public function applications()
    {
        return $this->hasMany(Application::class);
    }
}