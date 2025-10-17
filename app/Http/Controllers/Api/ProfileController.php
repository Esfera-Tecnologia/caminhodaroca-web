<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdatePersonalDataRequest;
use App\Http\Requests\UpdateCategoriesRequest;
use App\Http\Requests\UpdatePhotoRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

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
            'state' => $data['state'] ?? '',
            'age_range' => $data['ageRange'] ?? null,
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
        
        // Verifica se é arquivo tradicional parseado pelo PHP
        $fileField = $this->findFileField($request);
        if ($fileField) {
            return $this->saveUploadedFile($request->file($fileField), $user);
        }
        
        // Se é multipart mas não foi parseado pelo PHP, processa manualmente
        if (str_contains($request->header('Content-Type', ''), 'multipart/form-data')) {
            return $this->processMultipartManually($request, $user);
        }
        
        // Verifica se é base64 string (JSON)
        if ($request->has('photo') && is_string($request->input('photo'))) {
            return $this->processBase64Image($request->input('photo'), $user);
        }

        return response()->json([
            'message' => 'Nenhuma foto válida foi enviada.'
        ], 400);
    }
    
    private function findFileField($request)
    {
        $possibleFields = ['photo', 'image', 'file', 'avatar', 'picture'];
        
        foreach ($possibleFields as $field) {
            if ($request->hasFile($field)) {
                return $field;
            }
        }
        
        return null;
    }
    
    private function saveUploadedFile($file, $user)
    {
        try {
            // Remove a foto anterior se existir
            if ($user->avatar && Storage::exists($user->avatar)) {
                Storage::delete($user->avatar);
            }

            $path = $file->store('avatars', 'public');
            $user->update(['avatar' => $path]);

            return response()->json([
                'message' => 'Foto de perfil atualizada com sucesso!',
                'filePath' => url(Storage::url($path))
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao salvar o arquivo: ' . $e->getMessage()
            ], 500);
        }
    }
    
    private function processMultipartManually($request, $user)
    {
        try {
            $content = $request->getContent();
            $contentType = $request->header('Content-Type');
            
            // Extrai o boundary
            preg_match('/boundary=(.+)$/', $contentType, $matches);
            if (!isset($matches[1])) {
                throw new \Exception('Boundary not found in Content-Type');
            }
            
            $boundary = '--' . $matches[1];
            $parts = explode($boundary, $content);
            
            foreach ($parts as $part) {
                if (strpos($part, 'Content-Disposition: form-data; name="photo"') !== false) {
                    // Encontrou o campo photo
                    $headerEndPos = strpos($part, "\r\n\r\n");
                    if ($headerEndPos === false) continue;
                    
                    $headers = substr($part, 0, $headerEndPos);
                    $fileData = substr($part, $headerEndPos + 4);
                    $fileData = rtrim($fileData, "\r\n");
                    
                    // Extrai informações do header
                    if (preg_match('/filename="([^"]*)"/', $headers, $filenameMatch)) {
                        $filename = $filenameMatch[1];
                        $extension = pathinfo($filename, PATHINFO_EXTENSION);
                        
                        // Valida se é imagem
                        if (!in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif'])) {
                            throw new \Exception('Tipo de arquivo não suportado');
                        }
                        
                        // Valida tamanho (2MB)
                        if (strlen($fileData) > 2048 * 1024) {
                            throw new \Exception('Arquivo muito grande (máximo 2MB)');
                        }
                        
                        // Remove a foto anterior se existir
                        if ($user->avatar && Storage::exists($user->avatar)) {
                            Storage::delete($user->avatar);
                        }
                        
                        // Salva o arquivo
                        $newFilename = 'avatar_' . $user->id . '_' . time() . '.' . $extension;
                        $path = 'avatars/' . $newFilename;
                        
                        Storage::disk('public')->put($path, $fileData);
                        $user->update(['avatar' => $path]);

                        return response()->json([
                            'message' => 'Foto de perfil atualizada com sucesso!',
                            'filePath' => url(Storage::url($path))
                        ]);
                    }
                }
            }
            
            throw new \Exception('Campo photo não encontrado no multipart');
            
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao processar o arquivo: ' . $e->getMessage()
            ], 500);
        }
    }
    
    private function processBase64Image($photoData, $user)
    {
        try {
            // Remove o prefixo data:image/xxx;base64, se existir
            if (strpos($photoData, 'data:image') === 0) {
                $photoData = substr($photoData, strpos($photoData, ',') + 1);
            }
            
            // Decodifica o base64
            $imageData = base64_decode($photoData);
            if ($imageData === false) {
                throw new \Exception('Dados base64 inválidos');
            }
            
            // Detecta o tipo de imagem
            $imageInfo = getimagesizefromstring($imageData);
            if (!$imageInfo) {
                throw new \Exception('Dados de imagem inválidos');
            }
            
            $extension = match($imageInfo['mime']) {
                'image/jpeg' => 'jpg',
                'image/png' => 'png',
                'image/gif' => 'gif',
                default => throw new \Exception('Tipo de imagem não suportado: ' . $imageInfo['mime'])
            };
            
            // Remove a foto anterior se existir
            if ($user->avatar && Storage::exists($user->avatar)) {
                Storage::delete($user->avatar);
            }
            
            // Salva o arquivo
            $filename = 'avatar_' . $user->id . '_' . time() . '.' . $extension;
            $path = 'avatars/' . $filename;
            
            Storage::disk('public')->put($path, $imageData);
            $user->update(['avatar' => $path]);

            return response()->json([
                'message' => 'Foto de perfil atualizada com sucesso!',
                'filePath' => url(Storage::url($path))
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao processar a imagem: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete the user's account.
     */
    public function deleteAccount(Request $request)
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        $user->update([
            'email' => 'usuario@deletado',
            'nome' => 'Usuário deletado',
            'password'  => bcrypt(Str::random(40)),
            'avatar' => ''
        ]);
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        $user->tokens()->delete();
    }
}
