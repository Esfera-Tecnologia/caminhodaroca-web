<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Traits\ApiValidationResponse;

class RegisterFinishRequest extends FormRequest
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

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        // Mescla as regras das etapas 1 e 2
        $rules = array_merge(
            (new RegisterPersonalDataRequest($this->all()))->rules(),
            (new RegisterCategoriesRequest($this->all()))->rules()
        );

        // Adiciona regras específicas da etapa final (terms)
        $rules['terms'] = 'required|boolean|accepted';

        return $rules;
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'name' => 'nome',
            'email' => 'email',
            'password' => 'senha',
            'state' => 'estado',
            'ageRange' => 'faixa etária',
            'travelWith' => 'viajar com',
            'category' => 'categoria',
            'subcategories' => 'subcategorias',
            'terms' => 'termos de uso',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        // Mescla as mensagens das etapas 1 e 2
        $messages = array_merge(
            (new RegisterPersonalDataRequest())->messages(),
            (new RegisterCategoriesRequest())->messages()
        );

        // Adiciona mensagens específicas da etapa final
        $messages['terms.required'] = 'Você deve aceitar os termos para continuar.';
        $messages['terms.accepted'] = 'Você deve aceitar os termos para continuar.';

        return $messages;
    }
}
