<?php

namespace App\Models;

use App\Enums\PartnerStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Partner extends Model
{
    use HasFactory;

    protected $table = "partners";
    protected $primaryKey = "id";

    protected $fillable = [
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
        'status' => PartnerStatus::class,
    ];

    public function events(): HasMany
    {
        return $this->hasMany(PartnerEvent::class);
    }

    public function preapproved_partner(): HasMany
    {
        return $this->hasMany(PreapprovedPartner::class);
    }

    public function getLogoUrlAttribute(): string
    {
        return url('storage/'.$this->attributes['logo']);
    }

    public function cities(): BelongsToMany
    {
        return $this->belongsToMany(City::class, 'partner_city');
    }

    public function citiesRelationship(): HasMany
    {
        return $this->hasMany(PartnerCity::class);
    }

    public function scopeCities($query, $city_id = [])
    {
        if($city_id === [])
            return $query;
        return  $query->whereHas("citiesRelationship", function($query) use($city_id) {
            return $query->whereIn('city_id', $city_id);
        });
    }

    public function scopeKeyword($query, $keyword)
    {
        if ($keyword) {
            $query->where(function ($query) use ($keyword) {
                $query->where('name', 'like', "%{$keyword}%");
                $query->orWhereHas('cities', function ($query) use ($keyword) {
                    $query->where('name', 'like', "%{$keyword}%");
                });
            });
        }

        return $query;
    }


}
