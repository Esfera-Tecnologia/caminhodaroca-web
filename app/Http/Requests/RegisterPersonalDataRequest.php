<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Traits\ApiValidationResponse;

class RegisterPersonalDataRequest extends FormRequest
{
    use ApiValidationResponse;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'state' => 'required|string|size:2',
            'ageRange' => 'required|string|in:FROM_18_TO_29,FROM_30_TO_39,FROM_40_TO_49,FROM_50_TO_59,OVER_60',
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
            'password.required' => 'O campo senha é obrigatório.',
            'password.min' => 'A senha deve ter no mínimo 8 caracteres.',
            'state.required' => 'O campo estado é obrigatório.',
            'state.size' => 'O estado deve ter exatamente 2 caracteres.',
            'ageRange.required' => 'O campo faixa etária é obrigatório.',
            'ageRange.in' => 'A faixa etária selecionada é inválida.',
            'travelWith.in' => 'A opção de viagem selecionada é inválida.',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'nome',
            'email' => 'email',
            'password' => 'senha',
            'state' => 'estado',
            'ageRange' => 'faixa etária',
            'travelWith' => 'viajar com',
        ];
    }
}
