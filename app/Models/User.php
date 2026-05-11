<?php

namespace App\Models;

use App\Enums\AgeRange;
use App\Enums\TravelWith;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\FavoriteList;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * Boot do modelo para automações
     */
    protected static function booted()
    {
        static::created(function ($user) {
            $user->favoriteLists()->create([
                'name' => 'Favoritos',
                'is_default' => true,
            ]);
        });
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'can_approve_property',
        'status',
        'state',
        'age_range',
        'travel_with',
        'category_id',
        'avatar',
        'registration_source',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'age_range' => AgeRange::class,
            'travel_with' => 'json',
            'can_approve_property' => 'boolean',
        ];
    }

    public function profiles()
    {
        return $this->BelongsToMany(AccessProfile::class, 
            'user_has_access_profile',
            'user_id',
            'access_profile_id',
        );
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function subcategories()
    {
        return $this->belongsToMany(Subcategory::class, 'user_subcategories');
    }

    public function partner()
    {
        return $this->hasMany(Partner::class, 'user_id');
    }

    public function favoriteProperties()
    {
        return $this->belongsToMany(Property::class, 'user_favorite_properties');
    }

    /**
     * Relacionamento com as listas de favoritos do usuário
     */
    public function favoriteLists()
    {
        return $this->hasMany(FavoriteList::class);
    }

    /**
     * Relacionamento com as propriedades visitadas (Check-in)
     */
    public function visitedProperties()
    {
        return $this->belongsToMany(Property::class, 'property_visits')
                    ->withPivot('checkin_latitude', 'checkin_longitude')
                    ->withTimestamps();
    }

    public function isResponsible(): bool
    {
        return $this->profiles()->where('access_profiles.nome', 'Responsável')->exists();
    }

    public function isPartner(): bool
    {
        return $this->profiles()->where('access_profiles.nome', 'Parceiro')->exists();
    }
}
