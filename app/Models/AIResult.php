<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AIResult extends Model
{
    use HasFactory;

    protected $guarded = [];

    /** AIResult belongs to the Document it was generated for. */
    public function document()
    {
        return $this->belongsTo(Document::class);
    }
}