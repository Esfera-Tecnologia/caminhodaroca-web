<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    /**
     * Lista usuários filtrados por fonte de registro
     */
    public function users(Request $request): JsonResponse
    {
        $query = User::query();

        // Filtro por fonte de registro
        if ($source = $request->query('source')) {
            if (in_array($source, ['web', 'api'])) {
                $query->where('registration_source', $source);
            }
        }

        // Filtro por estado
        if ($state = $request->query('state')) {
            $query->where('state', $state);
        }

        // Filtro por faixa etária
        if ($ageRange = $request->query('ageRange')) {
            $query->where('age_range', $ageRange);
        }

        $users = $query->with(['category'])
            ->orderBy('created_at', 'desc')
            ->paginate($request->query('per_page', 15));

        return response()->json([
            'data' => $users->items(),
            'current_page' => $users->currentPage(),
            'last_page' => $users->lastPage(),
            'per_page' => $users->perPage(),
            'total' => $users->total(),
        ]);
    }

    /**
     * Estatísticas de usuários por fonte de registro
     */
    public function userStats(): JsonResponse
    {
        $webUsers = User::where('registration_source', 'web')->count();
        $apiUsers = User::where('registration_source', 'api')->count();
        $totalUsers = User::count();

        // Estatísticas por estado (apenas usuários da API)
        $apiUsersByState = User::where('registration_source', 'api')
            ->selectRaw('state, count(*) as total')
            ->whereNotNull('state')
            ->groupBy('state')
            ->orderBy('total', 'desc')
            ->get();

        // Estatísticas por faixa etária (apenas usuários da API)
        $apiUsersByAge = User::where('registration_source', 'api')
            ->selectRaw('age_range, count(*) as total')
            ->whereNotNull('age_range')
            ->groupBy('age_range')
            ->orderBy('total', 'desc')
            ->get();

        return response()->json([
            'users_summary' => [
                'total' => $totalUsers,
                'web' => $webUsers,
                'api' => $apiUsers,
                'web_percentage' => $totalUsers > 0 ? round(($webUsers / $totalUsers) * 100, 2) : 0,
                'api_percentage' => $totalUsers > 0 ? round(($apiUsers / $totalUsers) * 100, 2) : 0,
            ],
            'api_users_by_state' => $apiUsersByState,
            'api_users_by_age_range' => $apiUsersByAge,
        ]);
    }

    /**
     * Detalhes de um usuário específico
     */
    public function userDetails(int $id): JsonResponse
    {
        $user = User::with(['category', 'subcategories', 'favoriteProperties'])
            ->find($id);

        if (!$user) {
            return response()->json([
                'message' => 'Usuário não encontrado'
            ], 404);
        }

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'registration_source' => $user->registration_source,
            'state' => $user->state,
            'age_range' => $user->age_range,
            'travel_with' => $user->travel_with,
            'avatar' =>$user->avatar ? url(Storage::url($user->avatar)) : null,
            'category' => $user->category ? [
                'id' => $user->category->id,
                'name' => $user->category->name
            ] : null,
            'subcategories' => $user->subcategories->map(function ($subcategory) {
                return [
                    'id' => $subcategory->id,
                    'name' => $subcategory->name
                ];
            }),
            'favorite_properties_count' => $user->favoriteProperties->count(),
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
        ]);
    }
}
