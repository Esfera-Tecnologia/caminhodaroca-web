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
}

