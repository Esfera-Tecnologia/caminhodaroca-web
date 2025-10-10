<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\ForgotPasswordRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Storage;

class AuthController extends Controller
{
    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->validated();

        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return response()->json([
                'message' => 'Os dados informados são inválidos.',
                'errors' => [
                    'email' => ['Usuário não encontrado. Verifique suas credenciais de login e senha.']
                ]
            ], 422);
        }

        if ($user->status !== 'ativo') {
            return response()->json([
                'message' => 'Conta inativa.',
                'errors' => [
                    'email' => [
                        'Seu usuário está inativo. Entre em contato com o suporte para mais informações.'
                    ]
                ]
            ], 403);
        }

        // Revoga todos os tokens existentes
        $user->tokens()->delete();

        // Carrega as subcategorias do usuário
        $user->load('subcategories');

        // Cria um novo token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'avatar' => $user->avatar ? url(Storage::url($user->avatar)) : null,
            'email' => $user->email,
            'state' => $user->state,
            'ageRange' => $user->age_range,
            'travelWith' => $user->travel_with,
            'category' => $user->category_id,
            'subcategories' => $user->subcategories->pluck('id')->toArray(),
            'token' => $token,
            'source' => $user->registration_source
        ]);
    }

    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'message' => 'Usuário não encontrado.'
            ], 404);
        }

        Password::sendResetLink($request->only('email'));

        return response()->json([
            'message' => 'E-mail de recuperação enviado com sucesso.'
        ]);
    }

    public function logout(): JsonResponse
    {
        request()->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout realizado com sucesso.'
        ]);
    }
}
