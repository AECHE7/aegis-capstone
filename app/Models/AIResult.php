<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AIResult extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'anomaly_indicators' => 'array',
    ];

    /** AIResult belongs to the Document it was generated for. */
    public function document()
    {
        return $this->belongsTo(Document::class);
    }
}