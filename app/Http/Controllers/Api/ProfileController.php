<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdatePersonalDataRequest;
use App\Http\Requests\UpdateCategoriesRequest;
use App\Http\Requests\UpdatePhotoRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function updatePersonalData(UpdatePersonalDataRequest $request): JsonResponse
    {
        $user = request()->user();
        $data = $request->validated();
        
        // Prepara os dados para atualização (remove a senha completamente)
        $updateData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'state' => $data['state'],
            'age_range' => $data['ageRange'],
            'travel_with' => $data['travelWith'] ?? $user->travel_with,
        ];

        $user->update($updateData);

        return response()->json([
            'message' => 'As informações pessoais foram atualizadas com sucesso!'
        ]);
    }

    public function updateCategories(UpdateCategoriesRequest $request): JsonResponse
    {
        $user = request()->user();
        $data = $request->validated();

        $user->update([
            'category_id' => $data['category'],
        ]);

        // Atualiza as subcategorias
        if (isset($data['subcategories'])) {
            $user->subcategories()->sync($data['subcategories']);
        }

        return response()->json([
            'message' => 'As preferências foram atualizadas com sucesso!'
        ]);
    }

    public function updatePhoto(UpdatePhotoRequest $request): JsonResponse
    {
        $user = request()->user();
        
        if ($request->hasFile('photo')) {
            // Remove a foto anterior se existir
            if ($user->avatar && Storage::exists($user->avatar)) {
                Storage::delete($user->avatar);
            }

            // Salva a nova foto
            $path = $request->file('photo')->store('avatars', 'public');
            
            $user->update([
                'avatar' => $path
            ]);

            $filePath = Storage::url($path);

            return response()->json([
                'message' => 'Foto de perfil atualizada com sucesso!',
                'filePath' => $filePath
            ]);
        }

        return response()->json([
            'message' => 'Nenhuma foto foi enviada, mas a solicitação foi processada com sucesso.'
        ], 200);
    }
}
