<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterPersonalDataRequest;
use App\Http\Requests\RegisterCategoriesRequest;
use App\Http\Requests\RegisterFinishRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    /**
     * Etapa 1: Validar dados pessoais
     */
    public function personalData(RegisterPersonalDataRequest $request): JsonResponse
    {
        $request->validated();
        // Apenas valida os dados, não armazena nada
        return response()->json([
            'message' => 'Etapa 1 concluída com sucesso.'
        ]);
    }

    /**
     * Etapa 2: Validar categorias/subcategorias
     */
    public function categories(RegisterCategoriesRequest $request): JsonResponse
    {
        $request->validated();
        // Apenas valida os dados, não armazena nada
        return response()->json([
            'message' => 'Etapa 2 concluída com sucesso.'
        ]);
    }

    /**
     * Finalização: Criar usuário com todos os dados validados
     */
    public function finish(RegisterFinishRequest $request): JsonResponse
    {
        $data = $request->validated();

        // Cria o usuário
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'state' => $data['state'],
            'age_range' => $data['ageRange'],
            'travel_with' => $data['travelWith'] ?? null,
            'category_id' => $data['category'],
            'avatar' => 'https://picsum.photos/200/300',
            'registration_source' => 'api',
            'status' => 1,
            'access_profile_id' => 0, // Perfil padrão de usuário
        ]);

        // Associa as subcategorias se fornecidas
        if (!empty($data['subcategories'])) {
            $user->subcategories()->sync($data['subcategories']);
        }

        // Cria o token de autenticação
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'avatar' => $user->avatar,
            'email' => $user->email,
            'state' => $user->state,
            'ageRange' => $user->age_range,
            'travelWith' => $user->travel_with,
            'category' => $user->category_id,
            'subcategories' => $data['subcategories'] ?? [],
            'token' => $token,
        ], 201);
    }
}
