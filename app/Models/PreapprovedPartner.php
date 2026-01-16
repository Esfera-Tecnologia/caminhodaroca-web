<?php

namespace App\Models;

use App\Enums\PreapprovedPartnerStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PreapprovedPartner extends Model
{
    protected $table = "preapproved_partners";
    protected $primaryKey = "id";

    protected $fillable = [
        'partner_id',
        'user_id',
        'name',
        'email',
        'description',
        'logo',
        'instagram',
        'site',
        'routes',
        'circuits',
        'attractions',
        'status',
    ];

    protected $casts = [
        'status' => PreapprovedPartnerStatus::class,
    ];

    public function events(): HasMany
    {
        return $this->hasMany(PreapprovedPartnerEvent::class, 'preapproved_partner_id');
    }

    public function cities(): BelongsToMany
    {
        return $this->belongsToMany(City::class, 'preapproved_partner_city');
    }

    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class, 'partner_id', 'id');
    }

    public function getLogoUrlAttribute(): string
    {
        return url('storage/'.$this->attributes['logo']);
    }
}
