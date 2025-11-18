<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PartnerEvent extends Model
{
    protected $table = 'events';
    protected $fillable = [
        'partner_id',
        'description',
        'url',
    ];

    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    public function images(): PartnerEvent|HasMany
    {
        return $this->hasMany(EventImage::class,  'event_id');
    }

    public function preapproved_event(): PartnerEvent|HasMany
    {
        return $this->hasMany(PreapprovedPartnerEvent::class, 'event_id');
    }

}
