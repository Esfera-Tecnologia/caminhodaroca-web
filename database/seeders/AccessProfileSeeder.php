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
        // Perfil 1: Administrador de Sistema
        $adminProfile = AccessProfile::firstOrCreate(
            ['nome' => 'Administrador de Sistema'],
            [
                'descricao' => 'Controle total do sistema',
                'status' => 'ativo',
            ]
        );

        // Perfil 2: Usuário Comum
        $userProfile = AccessProfile::firstOrCreate(
            ['nome' => 'Usuário'],
            [
                'descricao' => 'Usuário comum do sistema',
                'status' => 'ativo',
            ]
        );

        // Permissões para Admin (todos os menus)
        foreach (Menu::all() as $menu) {
            AccessProfileMenuPermission::updateOrCreate(
                [
                    'access_profile_id' => $adminProfile->id,
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

        // Permissões para Usuário (apenas visualização limitada)
        // Usuários comuns não têm permissões específicas de menu
        // Suas permissões são controladas via middleware e policies
    }
}
