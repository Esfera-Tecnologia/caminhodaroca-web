<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Menu extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'slug',
    ];

    /**
     * Permissões associadas a esse menu
     */
    public function permissions()
    {
        return $this->hasMany(ProfileMenuPermission::class);
    }
}
