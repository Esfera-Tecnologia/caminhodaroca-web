<?php

namespace App\Models;

use App\Enums\PartnerStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
        'approved',
    ];

    protected $casts = [
        'status' => PartnerStatus::class,
        'approved' => 'boolean'
    ];

    protected static function booted()
    {
        static::updated(function ($partner) {
            if ($partner->isDirty('status')) {
                if ($partner->user) {
                    $userStatus = $partner->status === PartnerStatus::ATIVO ? 'ativo' : 'inativo';
                    $partner->user->update(['status' => $userStatus]);
                }
            }
        });
    }

    public function events(): HasMany
    {
        return $this->hasMany(PartnerEvent::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
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

    public function scopeActive($query)
    {
        return $query->where('status', PartnerStatus::ATIVO);
    }

    public function scopeApproved($query)
    {
        return $query->where('approved', true);
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
