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

    public function rules(): array
    {
        $userId = Auth::id();
        $ageRangeValues = implode(',', AgeRange::values());
        $travelWithValues = implode(',', TravelWith::values());

        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $userId,
            'state' => 'required|string|size:2',
            'ageRange' => "required|string|in:{$ageRangeValues}",
            'travelWith' => "nullable|string|in:{$travelWithValues}",
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O campo nome é obrigatório.',
            'email.required' => 'O campo email é obrigatório.',
            'email.email' => 'O email deve ter um formato válido.',
            'email.unique' => 'O e-mail informado já está em uso.',
            'state.required' => 'O campo estado é obrigatório.',
            'ageRange.required' => 'O campo faixa etária é obrigatório.',
        ];
    }
}
