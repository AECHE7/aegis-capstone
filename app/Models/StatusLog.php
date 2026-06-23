<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StatusLog extends Model
{
    protected $fillable = [
        'application_id',
        'status',
        'remarks',
        'changed_by'
    ];

    /**
     * Relationship: A status log belongs to an application.
     */
    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    /**
     * Relationship: A status log is recorded/changed by a User (admin or student).
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
