<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AccessProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'descricao',
        'status',
    ];

    /**
     * Permissões associadas ao perfil
     */
   
    public function permissions()
    {
        return $this->hasMany(AccessProfileMenuPermission::class);
    }
    public function users()
    {
        return $this->hasMany(User::class, 'access_profile_id');
    }
}
