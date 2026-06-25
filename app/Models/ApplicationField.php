<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApplicationField extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id',
        'field_name',
        'field_value',
    ];

    /**
     * Cast the field value as encrypted for Capstone Data Privacy Act of 2012 alignment.
     */
    protected $casts = [
        'field_value' => 'encrypted',
    ];

    public function application()
    {
        return $this->belongsTo(Application::class);
    }
}
