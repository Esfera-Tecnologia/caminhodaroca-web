<?php

namespace Database\Seeders;

use App\Models\AccessProfile;
use App\Models\AccessProfileMenuPermission;
use App\Models\Menu;
use Illuminate\Database\Seeder;

class AccessProfileSeeder extends Seeder
{
    public function run(): void
    {
        $profile = AccessProfile::firstOrCreate(
            ['nome' => 'Administrador de Sistema'],
            [
                'descricao' => 'Controle total do sistema',
                'status' => 'ativo',
            ]
        );

        foreach (Menu::all() as $menu) {
            AccessProfileMenuPermission::updateOrCreate(
                [
                    'access_profile_id' => $profile->id,
                    'menu_id' => $menu->id
                ],
                [
                    'can_view' => true,
                    'can_create' => true,
                    'can_edit' => true,
                    'can_delete' => true,
                ]
            );
        }
    }
}
