<?php

namespace App\Models;

use App\Enums\PartnerStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Partner extends Model
{
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
        'city',
        'category_id',
        'subcategory_id',
        'routes',
        'circuits',
        'attractions',
        'status',
    ];

    protected $casts = [
        'status' => PartnerStatus::class,
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

    public function scopeCity($query, $city_id = [])
    {
        if($city_id === [])
            return $query;
        return  $query->whereIn('city', City::query()->whereIn('id', $city_id)->pluck('name')->toArray());
    }

    public function scopeCategory($query, $category_id = [])
    {
        if($category_id === [])
            return $query;
        return  $query->whereIn('category_id', $category_id);
    }

    public function scopeSubcategory($query, $subcategory_id = [])
    {
        if($subcategory_id === [])
            return $query;
        return  $query->whereIn('subcategory_id', $subcategory_id);
    }

    public function scopeKeyword($query, $keyword = null)
    {
        if($keyword === null)
            return $query;
        return  $query->where('name', 'like', '%'.$keyword.'%')->orWhere('city', 'like', '%'.$keyword.'%');
    }

}
