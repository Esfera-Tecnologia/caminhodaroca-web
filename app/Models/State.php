<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class State extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
    ];

    /**
     * Relacionamento com cidades
     */
    public function cities(): HasMany
    {
        return $this->hasMany(City::class);
    }
    
    /**
     * Accessor para formatar o retorno da API
     */
    public function getValueAttribute()
    {
        return $this->code;
    }

    /**
     * Accessor para formatar o retorno da API
     */
    public function getLabelAttribute()
    {
        return $this->name;
    }
}