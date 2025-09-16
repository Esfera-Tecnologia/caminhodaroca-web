<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Traits\ApiValidationResponse;

class UpdatePhotoRequest extends FormRequest
{
    use ApiValidationResponse;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Se receber como JSON base64, aceita string
        if ($this->isJson() || $this->header('Content-Type') === 'application/json') {
            return [
                'photo' => 'required|string',
            ];
        }
        
        // Se é multipart mas PHP não parseia, permite processamento manual no controller
        if (str_contains($this->header('Content-Type', ''), 'multipart/form-data')) {
            return []; // Sem validação - processamento manual
        }
        
        // Verifica se tem arquivo em qualquer campo comum
        $possibleFields = ['photo', 'image', 'file', 'avatar', 'picture'];
        foreach ($possibleFields as $field) {
            if ($this->hasFile($field)) {
                return [
                    $field => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
                ];
            }
        }
        
        // Fallback para o campo esperado
        return [
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'photo.required' => 'A foto é obrigatória.',
            'photo.image' => 'O arquivo deve ser uma imagem.',
            'photo.mimes' => 'A foto deve ser do tipo: jpeg, png, jpg ou gif.',
            'photo.max' => 'A foto deve ter no máximo 2 MB.',
        ];
    }
}
