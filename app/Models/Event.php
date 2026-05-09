<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'partner_id',
        'name',
        'description',
        'start_date',
        'end_date',
        'state_id',
        'city_id',
        'organization',
        'full_description',
        'image',
        'is_highlight',
        'url',
        'status',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_highlight' => 'boolean',
    ];

    /**
     * Relacionamento com Propriedades (Muitos para Muitos)
     */
    public function properties(): BelongsToMany
    {
        return $this->belongsToMany(Property::class, 'event_has_property');
    }

    /**
     * Relacionamento com Parceiro
     */
    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    /**
     * Relacionamento com Estado
     */
    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    /**
     * Relacionamento com Cidade
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    /**
     * Accessor para a URL da imagem de capa
     */
    public function getImageUrlAttribute()
    {
        return $this->image ? asset('/storage/' . $this->image) : asset('assets/teste1.png');
    }
}
