<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
  protected $fillable = ['nome', 'status'];

  
    public function properties()
  {
      return $this->belongsToMany(Property::class, 'property_product');
  }

}
