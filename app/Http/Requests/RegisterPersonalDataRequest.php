<?php

namespace App\Http\Requests;

use App\Enums\AgeRange;
use App\Enums\TravelWith;
use Illuminate\Foundation\Http\FormRequest;
use App\Http\Traits\ApiValidationResponse;

class RegisterPersonalDataRequest extends FormRequest
{
    use ApiValidationResponse;

    protected function prepareForValidation()
    {
        // Normaliza campos se necessário
        if ($this->ageRange) {
            $this->merge(['ageRange' => strtoupper($this->ageRange)]);
        }
        if ($this->travelWith) {
            if (is_array($this->travelWith)) {
                $this->merge(['travelWith' => array_map('strtoupper', $this->travelWith)]);
            } else {
                $this->merge(['travelWith' => [strtoupper($this->travelWith)]]);
            }
        }
        if ($this->state) {
            $this->merge(['state' => strtoupper($this->state)]);
        }
    }

    public function rules(): array
    {
        $ageRangeValues = implode(',', AgeRange::values());
        $travelWithValues = implode(',', TravelWith::values());
        
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => [
                'required',
                'string',
                'min:8',
                function ($attribute, $value, $fail) {
                    $hasUpper = preg_match('/[A-Z]/', $value);
                    $hasLower = preg_match('/[a-z]/', $value);
                    $hasNumber = preg_match('/[0-9]/', $value);
                    $hasSpecial = preg_match('/[!@#$%&*()_+\-=\[\]{};\':"\\|,.<>\/?]/', $value);
                    
                    $typesCount = $hasUpper + $hasLower + $hasNumber + $hasSpecial;
                    
                    if ($typesCount < 2) {
                        $fail('A senha deve conter pelo menos 2 dos seguintes tipos de caracteres: letras maiúsculas (A-Z), letras minúsculas (a-z), números (0-9) ou caracteres especiais (!@#$%&*, etc.).');
                    }
                }
            ],
            'state' => 'required|string|size:2',
            'ageRange' => "required|string|in:{$ageRangeValues}",
            'travelWith' => 'nullable|array|min:1',
            'travelWith.*' => "required|string|in:{$travelWithValues}",
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
            'travelWith.array' => 'As opções de viagem devem ser uma lista.',
            'travelWith.min' => 'Você deve selecionar pelo menos uma opção de viagem.',
            'travelWith.*.required' => 'Cada opção de viagem é obrigatória.',
            'travelWith.*.string' => 'Cada opção de viagem deve ser uma string.',
            'travelWith.*.in' => 'Uma ou mais opções de viagem selecionadas são inválidas.',
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
