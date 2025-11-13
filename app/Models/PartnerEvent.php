<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PartnerEvent extends Model
{
    protected $table = 'events';
    protected $fillable = [
        'partner_id',
        'name',
        'description',
        'url',
    ];

    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    public function images()
    {
        return $this->hasMany(EventImage::class,  'event_id');
    }

}
