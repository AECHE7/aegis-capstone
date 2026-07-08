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
        'bank_name',
        'bank_account_name',
        'bank_account_number',
    ];

    protected $casts = [
        'clsu_id_number' => 'encrypted',
        'contact_number' => 'encrypted',
        'bank_account_number' => 'encrypted',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}