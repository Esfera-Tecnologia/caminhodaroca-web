<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\FavoriteListResource;
use App\Models\FavoriteList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteListController extends Controller
{
    /**
     * Lista todas as listas de favoritos do usuário logado
     */
    public function index()
    {
        $lists = Auth::user()->favoriteLists()->orderBy('is_default', 'desc')->orderBy('name', 'asc')->get();
        return FavoriteListResource::collection($lists);
    }

    /**
     * Cria uma nova lista de favoritos
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        // Verificar duplicidade de nome para o mesmo usuário
        $exists = Auth::user()->favoriteLists()->where('name', $request->name)->exists();
        if ($exists) {
            return response()->json([
                'message' => 'Você já possui uma lista com este nome.'
            ], 422);
        }

        $list = Auth::user()->favoriteLists()->create([
            'name' => $request->name,
            'is_default' => false,
        ]);

        return new FavoriteListResource($list);
    }

    /**
     * Exclui uma lista de favoritos (não permite excluir a padrão)
     */
    public function destroy($id)
    {
        $list = Auth::user()->favoriteLists()->findOrFail($id);

        if ($list->is_default) {
            return response()->json([
                'message' => 'A lista padrão "Favoritos" não pode ser excluída.'
            ], 403);
        }

        // Ao deletar a lista, o Laravel já remove os vínculos na tabela pivô devido ao onDelete('cascade') na migration
        $list->delete();

        return response()->json([
            'message' => 'Lista excluída com sucesso.'
        ]);
    }
}
