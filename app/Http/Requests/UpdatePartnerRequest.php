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
        $partner = $this->route('partner') ?? $this->route('id');
        $partnerId = $partner instanceof \App\Models\Partner ? $partner->id : $partner;

        $rule['email'] = [
            'required',
            'string',
            'email',
            'max:255',
            Rule::unique('partners', 'email')->ignore($partnerId),
        ];
        $rule['logo'] = [
            'nullable',
            'file',
            'max:2048',
        ];
        $rule['events.*.id'] = [
            'nullable',
            'int',
            'exists:partner_events,id',
        ];
        $rule['events.*.eventId'] = [
            'nullable',
            'int',
            'exists:partner_events,id',
        ];
        $rule['approve_updates'] = [
            'nullable',
        ];
        $rule['status'] = [
            'nullable',
            Rule::enum(\App\Enums\PartnerStatus::class),
        ];
        $rule['partner_category_id'] = [
            'required',
            'integer',
            Rule::exists('partner_categories', 'id')
                ->where(fn ($query) => $query->where('status', 'ativo')),
        ];

        // Regras para novos eventos para que sejam retornados no $request->validated()
        $rule['new_event_name'] = 'nullable|array';
        $rule['new_event_name.*'] = 'required|string|max:255';
        $rule['new_event_link'] = 'nullable|array';
        $rule['new_event_link.*'] = 'nullable|url|max:255';
        $rule['new_event_description'] = 'nullable|array';
        $rule['new_event_description.*'] = 'nullable|string';
        $rule['new_event_imagem'] = 'nullable|array';
        $rule['new_event_imagem.*'] = 'nullable|file|max:2048';

        return $rule;
    }
}
