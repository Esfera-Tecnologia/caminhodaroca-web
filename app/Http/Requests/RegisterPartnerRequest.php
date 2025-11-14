<?php

namespace App\Http\Requests;

use App\Enums\RioDeJaneiroCitiesEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterPartnerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
            ],
            'email' => [
                'required',
                'string',
                'email',
                'unique:partners,email',
            ],
            'description' => [
                'required',
                'string',
            ],
            'logo' => [
                'required',
                'file',
                'max:2048',
            ],
            'instagram' => [
                'nullable',
                'string',
                'url'
            ],
            'site' => [
                'nullable',
                'string',
                'url'
            ],
            'city' => [
                'required',
                'string',
                'in:'.implode(',', array_column(RioDeJaneiroCitiesEnum::cases(), 'value'))
            ],
            'category' => [
                'nullable',
                'integer',
                Rule::exists('categories', 'id')->where('status', 'ativo')
            ],
            'subcategory' => [
                'nullable',
                'integer',
                Rule::exists('subcategories', 'id')->where('status', 'ativo')
            ],
            'routes' => [
                'required',
                'string',
            ],
            'circuits' => [
                'required',
                'string',
            ],
            'attractions' => [
                'required',
                'string',
            ],
            'events' => [
                'nullable',
                'array',
            ],
            'events.*.name' => [
                'required',
                'string',
            ],
            'events.*.description' => [
                'nullable',
                'string',
            ],
            'events.*.images' => [
                'nullable',
                'array',
            ],
            'events.*.images.*' => [
                'nullable',
                'file',
                'max:2048',
            ],
            'events.*.url' => [
                'nullable',
                'string',
                'url'
            ],
        ];
    }
}
