<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PreapprovedPartnerEvent extends Model
{
    protected $table = 'preapproved_events';
    protected $fillable = [
        'preapproved_partner_id',
        'name',
        'description',
        'url',
    ];

    public function partner(): BelongsTo
    {
        return $this->belongsTo(PreapprovedPartner::class);
    }

    public function images()
    {
        return $this->hasMany(PreapprovedEventImage::class,  'preapproved_event_id');
    }

}
