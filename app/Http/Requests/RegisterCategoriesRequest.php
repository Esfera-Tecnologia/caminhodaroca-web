<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use App\Http\Traits\ApiValidationResponse;

class RegisterCategoriesRequest extends FormRequest
{
    use ApiValidationResponse;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category' => 'required|integer|exists:categories,id',
            'subcategories' => 'nullable|array',
            'subcategories.*' => 'integer|exists:subcategories,id',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            if ($this->has('category') && $this->has('subcategories')) {
                $categoryId = $this->input('category');
                $subcategoryIds = $this->input('subcategories', []);
                
                if (!empty($subcategoryIds)) {
                    $validSubcategories = \App\Models\Subcategory::where('category_id', $categoryId)
                        ->whereIn('id', $subcategoryIds)
                        ->pluck('id')
                        ->toArray();
                    
                    if (count($validSubcategories) !== count($subcategoryIds)) {
                        $validator->errors()->add('subcategories', 'Uma ou mais subcategorias não pertencem à categoria selecionada.');
                    }
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'category.required' => 'O campo categoria é obrigatório.',
            'category.integer' => 'A categoria deve ser um número válido.',
            'category.exists' => 'A categoria selecionada não existe.',
            'subcategories.array' => 'As subcategorias devem ser uma lista.',
            'subcategories.*.integer' => 'Cada subcategoria deve ser um número válido.',
            'subcategories.*.exists' => 'Uma ou mais subcategorias selecionadas não existem.',
        ];
    }

    public function attributes(): array
    {
        return [
            'category' => 'categoria',
            'subcategories' => 'subcategorias',
        ];
    }
}
