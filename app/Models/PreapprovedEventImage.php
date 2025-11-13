<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PreapprovedEventImage extends Model
{
    protected $table = 'preapproved_event_images';
    protected $fillable = [
        'preapproved_event_id',
        'image'
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(PreapprovedPartnerEvent::class);
    }
}
