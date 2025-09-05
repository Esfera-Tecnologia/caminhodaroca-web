<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class City extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'state_id',
    ];

    /**
     * Relacionamento com estado
     */
    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    /**
     * Accessor para formatar o retorno da API
     */
    public function getValueAttribute()
    {
        return $this->id;
    }

    /**
     * Accessor para formatar o retorno da API
     */
    public function getLabelAttribute()
    {
        return $this->name;
    }
}
