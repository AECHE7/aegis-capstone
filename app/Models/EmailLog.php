<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id',
        'recipient',
        'subject',
        'content',
    ];

    /**
     * Relationship: An email log belongs to an application.
     */
    public function application()
    {
        return $this->belongsTo(Application::class);
    }
}
