<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'clsu_id_number',
        'college',
        'course',
        'year_level',
        'contact_number',
        'guardian_name',
        'emergency_contact_number',
    ];

    protected $casts = [
        'clsu_id_number' => 'encrypted',
        'contact_number' => 'encrypted',
        'guardian_name' => 'encrypted',
        'emergency_contact_number' => 'encrypted',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}