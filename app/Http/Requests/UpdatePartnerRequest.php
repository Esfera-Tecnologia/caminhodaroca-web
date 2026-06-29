<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class UpdatePartnerRequest extends RegisterPartnerRequest
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
        $rule = parent::rules();
        $partner = $this->route('partner');

        $rule['email'] = [
            'required',
            'string',
            'email',
            'max:255',
            Rule::unique('partners', 'email')->ignore($partner?->id),
        ];
        $rule['logo'] = [
            'nullable',
            'file',
            'max:2048',
        ];
        $rule['events.*.id'] = [
            'nullable',
            'int',
            'exists:events,id',
        ];
        $rule['approve_updates'] = [
            'nullable',
        ];
        $rule['status'] = [
            'required',
            Rule::enum(\App\Enums\PartnerStatus::class),
        ];
        return $rule;
    }
}
