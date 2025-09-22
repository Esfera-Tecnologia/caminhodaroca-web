<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PropertyRating extends Model
{
    protected $fillable = [
        'user_id',
        'property_id',
        'rating'
    ];

    protected $casts = [
        'rating' => 'integer'
    ];

    /**
     * Get the user that gave the rating
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the property that was rated
     */
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }
}
