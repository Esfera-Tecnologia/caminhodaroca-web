<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Database\Seeder;

class CategorySubcategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Categorias
        $categories = [
            [
                'id' => 1,
                'name' => 'Tecnologia',
                'nome' => 'Tecnologia',
                'descricao' => 'Categoria relacionada à tecnologia',
                'status' => 1
            ],
            [
                'id' => 2,
                'name' => 'Saúde',
                'nome' => 'Saúde',
                'descricao' => 'Categoria relacionada à saúde e bem-estar',
                'status' => 1
            ],
            [
                'id' => 3,
                'name' => 'Finanças',
                'nome' => 'Finanças',
                'descricao' => 'Categoria relacionada a finanças',
                'status' => 1
            ],
            [
                'id' => 4,
                'name' => 'Educação',
                'nome' => 'Educação',
                'descricao' => 'Categoria relacionada à educação',
                'status' => 1
            ]
        ];

        foreach ($categories as $categoryData) {
            Category::updateOrCreate(
                ['id' => $categoryData['id']],
                $categoryData
            );
        }

        // Subcategorias
        $subcategories = [
            // Tecnologia
            ['id' => 1, 'category_id' => 1, 'name' => 'Smartphones', 'nome' => 'Smartphones', 'status' => 1],
            ['id' => 2, 'category_id' => 1, 'name' => 'Computadores', 'nome' => 'Computadores', 'status' => 1],
            ['id' => 3, 'category_id' => 1, 'name' => 'Tablets', 'nome' => 'Tablets', 'status' => 1],
            ['id' => 4, 'category_id' => 1, 'name' => 'Acessórios', 'nome' => 'Acessórios', 'status' => 1],
            
            // Saúde
            ['id' => 5, 'category_id' => 2, 'name' => 'Medicina Preventiva', 'nome' => 'Medicina Preventiva', 'status' => 1],
            ['id' => 6, 'category_id' => 2, 'name' => 'Fitness', 'nome' => 'Fitness', 'status' => 1],
            ['id' => 7, 'category_id' => 2, 'name' => 'Nutrição', 'nome' => 'Nutrição', 'status' => 1],
            
            // Finanças
            ['id' => 8, 'category_id' => 3, 'name' => 'Investimentos', 'nome' => 'Investimentos', 'status' => 1],
            ['id' => 9, 'category_id' => 3, 'name' => 'Economia Doméstica', 'nome' => 'Economia Doméstica', 'status' => 1],
            
            // Educação
            ['id' => 10, 'category_id' => 4, 'name' => 'Cursos Online', 'nome' => 'Cursos Online', 'status' => 1],
            ['id' => 11, 'category_id' => 4, 'name' => 'Livros', 'nome' => 'Livros', 'status' => 1],
        ];

        foreach ($subcategories as $subcategoryData) {
            Subcategory::updateOrCreate(
                ['id' => $subcategoryData['id']],
                $subcategoryData
            );
        }

        $this->command->info('Categorias e subcategorias criadas com sucesso!');
        $this->command->info('- 4 categorias: Tecnologia, Saúde, Finanças, Educação');
        $this->command->info('- 11 subcategorias distribuídas entre as categorias');
    }
}
