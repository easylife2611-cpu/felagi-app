<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attachment extends Model
{
    use HasUuids, SoftDeletes;

    public $timestamps = false;

    protected $fillable = [
        'uploaded_by',
        'need_id',
        'offer_id',
        'message_id',
        'purpose',
        'storage_disk',
        'storage_key',
        'original_name',
        'detected_mime',
        'byte_size',
        'sha256',
        'visibility',
        'scan_status',
        'created_at',
    ];

    protected $casts = [
        'byte_size' => 'integer',
        'created_at' => 'datetime',
    ];

    public const PURPOSE_PROFILE = 'PROFILE';
    public const PURPOSE_NEED = 'NEED';
    public const PURPOSE_OFFER = 'OFFER';
    public const PURPOSE_MESSAGE = 'MESSAGE';

    public const VISIBILITY_PUBLIC = 'PUBLIC';
    public const VISIBILITY_PRIVATE = 'PRIVATE';

    public const SCAN_PENDING = 'PENDING';
    public const SCAN_CLEAN = 'CLEAN';
    public const SCAN_REJECTED = 'REJECTED';

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function need()
    {
        return $this->belongsTo(Need::class);
    }

    public function offer()
    {
        return $this->belongsTo(Offer::class);
    }

    public function message()
    {
        return $this->belongsTo(Message::class);
    }

    public function scopeClean($query)
    {
        return $query->where('scan_status', self::SCAN_CLEAN);
    }

    public function isClean(): bool
    {
        return $this->scan_status === self::SCAN_CLEAN;
    }
}
