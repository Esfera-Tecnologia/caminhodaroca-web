<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    public function categories(): JsonResponse
    {
        $categories = Category::select('id as value', 'nome as label')
            ->where('status', 'ativo')
            ->orderBy('nome')
            ->get();

        return response()->json($categories);
    }

    public function subcategories(): JsonResponse
    {
        $query = Subcategory::select('id as value', 'nome as label')
            ->where('status', 'ativo')
            ->orderBy('nome');

        // Suporte a múltiplos formatos de filtro por categoria
        $categoryIds = $this->getCategoryIdsFromRequest();
        
        if (!empty($categoryIds)) {
            $query->whereIn('category_id', $categoryIds);
        }

        $subcategories = $query->get();

        return response()->json($subcategories);
    }

    /**
     * Extrai os IDs de categoria dos parâmetros da requisição
     * Suporta múltiplos formatos: categoryIds[], category, categories
     */
    private function getCategoryIdsFromRequest(): array
    {
        $request = request();
        
        // Formato 1: categoryIds[] (array)
        if ($request->has('categoryIds')) {
            $categoryIds = $request->query('categoryIds', []);
            return is_array($categoryIds) ? $categoryIds : [$categoryIds];
        }
        
        // Formato 2: category (único)
        if ($request->has('category')) {
            return [(int) $request->query('category')];
        }
        
        // Formato 3: categories (string separada por vírgula)
        if ($request->has('categories')) {
            $categories = $request->query('categories');
            return array_map('intval', explode(',', $categories));
        }
        
        return [];
    }
}
