<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NeedAward extends Model
{
    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = 'need_id';
    protected $keyType = 'string';

    protected $fillable = [
        'need_id',
        'offer_id',
        'accepted_by',
        'accepted_at',
        'request_id',
    ];

    protected $casts = [
        'accepted_at' => 'datetime',
    ];

    public function need()
    {
        return $this->belongsTo(Need::class);
    }

    public function offer()
    {
        return $this->belongsTo(Offer::class);
    }

    public function acceptedBy()
    {
        return $this->belongsTo(User::class, 'accepted_by');
    }
}
