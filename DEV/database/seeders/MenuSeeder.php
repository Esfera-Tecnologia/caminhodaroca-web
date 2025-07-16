<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        $menus = [
            [
                'nome' => 'Dashboard',
                'slug' => 'dashboard',
                'icone' => 'fas fa-chart-line',
            ],
            [
                'nome' => 'Perfil de Acesso',
                'slug' => 'access-profiles',
                'icone' => 'fas fa-user-shield',
            ],
            [
                'nome' => 'Usuário',
                'slug' => 'users',
                'icone' => 'fas fa-user',
            ],
            [
                'nome' => 'Categoria',
                'slug' => 'categories',
                'icone' => 'fas fa-folder',
            ],
            [
                'nome' => 'Subcategoria',
                'slug' => 'subcategories',
                'icone' => 'fas fa-puzzle-piece',
            ],
            [
                'nome' => 'Propriedade',
                'slug' => 'properties',
                'icone' => 'fas fa-tractor',
            ],
        ];

        foreach ($menus as $menu) {
            Menu::updateOrCreate(
                ['slug' => $menu['slug']],
                ['nome' => $menu['nome'], 'icone' => $menu['icone']]
            );
        }
    }
}
