<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExportAccessLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'export_type',
        'date_from',
        'date_to',
        'format',
        'ip_address',
    ];

    protected $casts = [
        'date_from' => 'date',
        'date_to'   => 'date',
    ];

    /** The superadmin who triggered the export. */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
