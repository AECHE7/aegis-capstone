<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScholarshipField extends Model
{
    use HasFactory;

    protected $fillable = [
        'scholarship_id',
        'field_name',
        'field_label',
        'field_type',
        'is_required',
        'options',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'options' => 'array', // automatically decode json options to array
    ];

    public function scholarship()
    {
        return $this->belongsTo(Scholarship::class);
    }
}
