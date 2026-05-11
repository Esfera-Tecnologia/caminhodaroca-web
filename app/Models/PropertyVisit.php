<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PropertyVisit extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'property_id',
        'checkin_latitude',
        'checkin_longitude',
    ];

    /**
     * Relacionamento com o Usuário
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relacionamento com a Propriedade
     */
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }
}
