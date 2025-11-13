<?php

namespace App\Models;

use App\Enums\PreapprovedPartnerStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
        'city',
        'category_id',
        'subcategory_id',
        'routes',
        'circuits',
        'attractions',
        'status',
    ];

    protected $casts = [
        'status' => PreapprovedPartnerStatus::class,
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(Subcategory::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(PreapprovedPartnerEvent::class);
    }
}
