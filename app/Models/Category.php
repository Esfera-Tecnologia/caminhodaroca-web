<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['nome', 'name', 'descricao', 'description', 'status'];

    // Accessor para compatibilidade
    public function getNameAttribute()
    {
        return $this->attributes['name'] ?? $this->nome;
    }

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

    public function users()
    {
        return $this->hasMany(User::class);
    }
}

