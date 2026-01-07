<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PartnerEvent extends Model
{
    use HasFactory;
    
    protected $table = 'events';
    protected $fillable = [
        'partner_id',
        'name',
        'description',
        'url',
        'status'
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

    public function scopeApproved($query)
    {
        return $query->where('status', '!=', 'pending');
    }
}
