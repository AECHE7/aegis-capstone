<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id',
        'file_path',
        'original_name',
        'document_type',
    ];


    // A Document has one AI Result
    public function aiResult()
    {
        return $this->hasOne(AIResult::class);
    }

    // A Document belongs to an Application
    public function application()
    {
        return $this->belongsTo(Application::class);
    }

}