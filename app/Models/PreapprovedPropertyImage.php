<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PreapprovedPropertyImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'preapproved_property_id',
        'path',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function getImageAttribute()
    {
        return asset('/storage/' . $this->path);
    }
}
