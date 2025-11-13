<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventImage extends Model
{
    protected $table = 'event_images';
    protected $fillable = [
        'event_id',
        'image'
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(PartnerEvent::class);
    }
}
