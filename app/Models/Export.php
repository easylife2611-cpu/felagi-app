<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Export extends Model
{
    use HasFactory, HasUuids;

    public $timestamps = false;

    protected $fillable = [
        'comparison_id', 'requester_id', 'format', 'status',
        'storage_key', 'expires_at', 'created_at', 'completed_at',
    ];

    protected $casts = [
        'expires_at'   => 'datetime',
        'created_at'   => 'datetime',
        'completed_at' => 'datetime',
    ];

    public const STATUS_PENDING    = 'PENDING';
    public const STATUS_PROCESSING = 'PROCESSING';
    public const STATUS_READY      = 'READY';
    public const STATUS_FAILED     = 'FAILED';
    public const STATUS_EXPIRED    = 'EXPIRED';

    public const FORMAT_PDF  = 'PDF';
    public const FORMAT_XLSX = 'XLSX';
    public const FORMAT_CSV  = 'CSV';
    public const FORMAT_TXT  = 'TXT';
}
