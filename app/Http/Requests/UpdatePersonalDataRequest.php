<?php

namespace App\Http\Requests;

use App\Enums\AgeRange;
use App\Enums\TravelWith;
use Illuminate\Foundation\Http\FormRequest;
use App\Http\Traits\ApiValidationResponse;
use Illuminate\Support\Facades\Auth;

class UpdatePersonalDataRequest extends FormRequest
{
    use ApiValidationResponse;

    public function authorize(): bool
    {
        return true;
    }

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
        $userId = Auth::id();
        $ageRangeValues = implode(',', AgeRange::values());
        $travelWithValues = implode(',', TravelWith::values());

        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $userId,
            'state' => 'nullable|string|size:2',
            'ageRange' => "nullable|string|in:{$ageRangeValues}",
            'travelWith' => 'nullable|array',
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
            'ageRange.in' => 'A faixa etária selecionada é inválida.',
            'travelWith.array' => 'As opções de viagem devem ser uma lista.',
            'travelWith.min' => 'Você deve selecionar pelo menos uma opção de viagem.',
            'travelWith.*.required' => 'Cada opção de viagem é obrigatória.',
            'travelWith.*.string' => 'Cada opção de viagem deve ser uma string.',
            'travelWith.*.in' => 'Uma ou mais opções de viagem selecionadas são inválidas.',
        ];
    }
}
