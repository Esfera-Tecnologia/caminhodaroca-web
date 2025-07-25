<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AccessProfileMenuPermission extends Model
{
     use HasFactory;

    protected $table = 'access_profile_menu_permissions';

    protected $fillable = [
        'access_profile_id',
        'menu_id',
        'can_view',
        'can_create',
        'can_edit',
        'can_delete',
    ];

    /**
     * Perfil vinculado
     */
    public function AccessProfile()
    {
        return $this->belongsTo(AccessProfile::class);
    }

    /**
     * Menu vinculado
     */
    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }
}
