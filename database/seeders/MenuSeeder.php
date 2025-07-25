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
                'ordem' => 0,
            ],
            [
                'nome' => 'Perfil de Acesso',
                'slug' => 'access-profiles',
                'icone' => 'fas fa-user-shield',
                'ordem' => 1,
            ],
            [
                'nome' => 'Usuário',
                'slug' => 'users',
                'icone' => 'fas fa-user',
                  'ordem' => 2,
            ],
            [
                'nome' => 'Categoria',
                'slug' => 'categories',
                'icone' => 'fas fa-folder',
                  'ordem' => 3,
            ],
            [
                'nome' => 'Subcategoria',
                'slug' => 'subcategories',
                'icone' => 'fas fa-puzzle-piece',
                  'ordem' => 4,
            ],
            [
                'nome' => 'Produto',
                'slug' => 'products',
                'icone' => 'fab fa-product-hunt',
                  'ordem' => 5,
            ],
            [
                'nome' => 'Propriedade',
                'slug' => 'properties',
                'icone' => 'fas fa-tractor',
                  'ordem' => 6,
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
