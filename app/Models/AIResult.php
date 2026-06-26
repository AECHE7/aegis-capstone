<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AIResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_id',
        'fraud_probability',
        'classification',
        'heatmap_path',
    ];


    /** AIResult belongs to the Document it was generated for. */
    public function document()
    {
        return $this->belongsTo(Document::class);
    }
}