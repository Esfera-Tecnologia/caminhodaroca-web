<?php

namespace App\Models;

use App\Enums\AgeRange;
use App\Enums\TravelWith;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'access_profile_id',
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
            'travel_with' => TravelWith::class,
        ];
    }

    public function accessProfile()
    {
        return $this->belongsTo(AccessProfile::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function subcategories()
    {
        return $this->belongsToMany(Subcategory::class, 'user_subcategories');
    }

    public function favoriteProperties()
    {
        return $this->belongsToMany(Property::class, 'user_favorite_properties');
    }
}
