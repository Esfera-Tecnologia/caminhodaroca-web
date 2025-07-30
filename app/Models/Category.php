<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['nome', 'descricao', 'status'];

    public function subcategories()
    {
        return $this->hasMany(Subcategory::class);
    }
    public function properties()
    {
        return $this->belongsToMany(Property::class, 'category_property_subcategories')
                    ->withPivot('subcategory_id')
                    ->withTimestamps();
    }
}

