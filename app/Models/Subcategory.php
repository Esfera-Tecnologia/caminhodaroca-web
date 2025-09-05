<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subcategory extends Model
{
    protected $fillable = ['category_id', 'nome', 'name', 'status'];

    // Accessor para compatibilidade
    public function getNameAttribute()
    {
        return $this->attributes['name'] ?? $this->nome;
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    
    public function properties()
    {
        return $this->belongsToMany(Property::class, 'category_property_subcategories')
                    ->withPivot('category_id')
                    ->withTimestamps();
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_subcategories');
    }
}
