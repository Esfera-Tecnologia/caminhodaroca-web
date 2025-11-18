<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PreapprovedPartnerEvent extends Model
{
    protected $table = 'preapproved_events';
    protected $fillable = [
        'preapproved_partner_id',
        'event_id',
        'description',
        'url',
    ];

    public function partner(): BelongsTo
    {
        return $this->belongsTo(PreapprovedPartner::class);
    }

    public function images(): PreapprovedPartnerEvent|HasMany
    {
        return $this->hasMany(PreapprovedEventImage::class,  'preapproved_event_id');
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(PartnerEvent::class, 'event_id');
    }

}
