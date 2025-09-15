<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Http\JsonResponse;

class PropertyController extends Controller
{
    public function index(): JsonResponse
    {
        $query = Property::with(['categorias', 'subcategories', 'images']);

        if ($keyword = request()->query('keyword')) {
            $query->where(function($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                  ->orWhere('descricao_servico', 'like', "%{$keyword}%");
            });
        }

        if ($categories = request()->query('categories')) {
            // Garantir que é array
            if (!is_array($categories)) {
                $categories = is_string($categories) ? explode(',', $categories) : [$categories];
            }
            // Filtrar e converter para inteiros
            $categories = array_filter(array_map('intval', $categories));
            
            if (!empty($categories)) {
                $query->whereHas('categorias', function($q) use ($categories) {
                    $q->whereIn('categories.id', $categories);
                });
            }
        }

        if ($subcategories = request()->query('subcategories')) {
            // Garantir que é array
            if (!is_array($subcategories)) {
                $subcategories = is_string($subcategories) ? explode(',', $subcategories) : [$subcategories];
            }
            // Filtrar e converter para inteiros
            $subcategories = array_filter(array_map('intval', $subcategories));
            
            if (!empty($subcategories)) {
                $query->whereHas('subcategories', function($q) use ($subcategories) {
                    $q->whereIn('subcategories.id', $subcategories);
                });
            }
        }

        // Filtro por localização(sujeito a mudanças pois tem que enviar nome da cidade e nao o ID)
        if ($propertyLocationId = request()->query('propertyLocationId')) {
            $query->where('cidade', 'like', "%{$propertyLocationId}%");
        }

        $properties = $query->get();

        dd($properties);

        if ($properties->isEmpty()) {
        return response()->json([
            'message' => 'Propriedade não encontrada'
        ], 404);
    }

        $properties = $properties->map(function($property) {
            return [
                'id' => $property->id,
                'name' => $property->name,
                'logo' => $property->logo,
                'type' => $property->type ?? 'Propriedade Rural',
                'rating' => $property->rating ?? 0,
                'location' => [
                    'city' => $property->city ?? 'Cidade não informada',
                    'coordinates' => [
                        'lat' => $property->latitude ?? 0,
                        'lng' => $property->longitude ?? 0,
                    ]
                ],
            ];
        });

        return response()->json($properties);
    }

    public function show(int $id): JsonResponse
    {
        $property = Property::with(['categorias', 'subcategories', 'images'])->find($id);

        if (!$property) {
            return response()->json([
                'message' => 'Propriedade não encontrada'
            ], 404);
        }

        $gallery = $property->images->pluck('image')->toArray();
        if (empty($gallery)) {
            $gallery = [
                'https://picsum.photos/200/300',
                'https://picsum.photos/200/300',
                'https://picsum.photos/200/300',
            ];
        }

        return response()->json([
            'id' => $property->id,
            'name' => $property->name,
            'logo' => $property->logo,
            'phone' => $property->phone,
            'rating' => $property->rating ?? 0,
            'type' => $property->type ?? 'Propriedade Rural',
            'location' => [
                'city' => $property->city ?? 'Cidade não informada',
                'state' => $property->state ?? 'Estado não informado',
                'coordinates' => [
                    'lat' => $property->latitude ?? 0,
                    'lng' => $property->longitude ?? 0,
                ]
            ],
            'description' => $property->description ?? 'Descrição não disponível',
            'category' => $property->categorias->first()->name ?? 'Categoria não informada',
            'subcategory' => $property->subcategories->first()->name ?? 'Subcategoria não informada',
            'openingHours' => [
                'weekdays' => $property->weekday_hours ?? '08:00 às 18:00',
                'weekend' => $property->weekend_hours ?? '09:00 às 17:00',
            ],
            'products' => $property->products ?? 'Produtos não informados',
            'accessibility' => $property->accessibility ?? 'Informações de acessibilidade não disponíveis',
            'petPolicy' => $property->pet_policy ?? 'Política para animais não informada',
            'gallery' => $gallery,
        ]);
    }

    public function toggleFavorite(int $id): JsonResponse
    {
        $user = request()->user();
        
        if (!$user) {
            return response()->json([
                'message' => 'Usuário não autenticado'
            ], 401);
        }

        $property = Property::find($id);

        if (!$property) {
            return response()->json([
                'message' => 'Propriedade não encontrada'
            ], 404);
        }

        // Verifica se já está favoritado
        $isFavorited = $user->favoriteProperties()->where('property_id', $id)->exists();

        if ($isFavorited) {
            $user->favoriteProperties()->detach($id);
            return response()->json([
                'favorited' => false,
                'message' => 'Propriedade removida dos favoritos'
            ]);
        } else {
            $user->favoriteProperties()->attach($id);
            return response()->json([
                'favorited' => true,
                'message' => 'Propriedade adicionada aos favoritos'
            ]);
        }
    }
}
