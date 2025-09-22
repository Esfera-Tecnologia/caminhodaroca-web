<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Property extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'whatsapp',
        'status',
        'instagram',
        'endereco_principal',
        'endereco_secundario',
        'google_maps_url',
        'latitude',
        'longitude',
        'cidade',
        'descricao_servico',
        'certificacao',
        'vende_produtos_artesanais',
        'produtos_artesanais',
        'tipo_funcionamento',
        'observacoes_funcionamento',
        'agenda_personalizada',
        'aceita_animais',
        'possui_acessibilidade',
        'logo_path',
        'galeria_paths',
        'phone',
        'city',
        'state',
        'description',
        'type',
        'rating',
        'weekday_hours',
        'weekend_hours',
        'products_description',
        'accessibility',
        'pet_policy',
    ];

    protected $casts = [
        'certificacao' => 'integer',
        'vende_produtos_artesanais' => 'boolean',
        'produtos_artesanais' => 'array',
        'agenda_personalizada' => 'array',
        'galeria_paths' => 'array',
        'aceita_animais' => 'boolean',
        'possui_acessibilidade' => 'boolean',
        'rating' => 'decimal:1',
    ];

    // Accessors para compatibilidade
    public function getPhoneAttribute()
    {
        return $this->whatsapp;
    }

    public function getCityAttribute()
    {
        return $this->cidade;
    }

    public function getDescriptionAttribute()
    {
        return $this->descricao_servico;
    }

    public function getProductsDescriptionAttribute()
    {
        return $this->produtos_artesanais ? implode(', ', $this->produtos_artesanais) : null;
    }

    public function getAccessibilityAttribute()
    {
        return $this->possui_acessibilidade ? 'Acesso para cadeirantes, estacionamento disponível' : 'Sem informações de acessibilidade';
    }

    public function getPetPolicyAttribute()
    {
        return $this->aceita_animais ? 'Nosso estabelecimento permite a entrada de animais de estimação' : 'Animais não são permitidos';
    }

    public function getLogoAttribute()
    {
        return asset('/storage/' . $this->logo_path) ?: 'https://picsum.photos/200/300';
    }

    // Relacionamentos
    public function categorias()
    {
        return $this->belongsToMany(Category::class, 'category_property_subcategories')
                    ->withPivot('subcategory_id')
                    ->withTimestamps();
    }

    public function subcategories()
    {
        return $this->belongsToMany(Subcategory::class, 'category_property_subcategories')
                    ->withPivot('category_id')
                    ->withTimestamps();
    }

    public function subcategorias()
    {
        return $this->subcategories();
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'property_product');
    }

    public function images()
    {
        return $this->hasMany(PropertyImage::class);
    }

    public function favoritedByUsers()
    {
        return $this->belongsToMany(User::class, 'user_favorite_properties');
    }

    /**
     * Get all ratings for this property
     */
    public function ratings()
    {
        return $this->hasMany(PropertyRating::class);
    }

    /**
     * Get the average rating for this property
     */
    public function getAverageRatingAttribute()
    {
        return $this->ratings()->avg('rating') ?? 0;
    }

    /**
     * Get rating for a specific user
     */
    public function getUserRating($userId)
    {
        return $this->ratings()->where('user_id', $userId)->first()?->rating;
    }
}

