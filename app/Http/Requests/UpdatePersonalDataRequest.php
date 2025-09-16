<?php

namespace App\Http\Requests;

use App\Enums\AgeRange;
use Illuminate\Foundation\Http\FormRequest;
use App\Http\Traits\ApiValidationResponse;

class UpdatePersonalDataRequest extends FormRequest
{
    use ApiValidationResponse;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = \Illuminate\Support\Facades\Auth::id();
        $ageRangeValues = implode(',', AgeRange::values());
        
        return [
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:users,email,' . $userId,
            'password' => 'nullable|string|min:8',
            'state' => 'nullable|string|size:2',
            'ageRange' => "nullable|string|in:{$ageRangeValues}",
            'travelWith' => 'nullable|string|in:ALONE,COUPLE,FAMILY,FRIENDS,GROUPS',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O campo nome é obrigatório.',
            'email.required' => 'O campo email é obrigatório.',
            'email.email' => 'O email deve ter um formato válido.',
            'email.unique' => 'O e-mail informado já está em uso.',
            'password.min' => 'A senha deve ter no mínimo 8 caracteres.',
            'state.required' => 'O campo estado é obrigatório.',
            'ageRange.required' => 'O campo faixa etária é obrigatório.',
        ];
    }
}
