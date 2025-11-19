<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class RegisterPartnerRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'instagram' => $this->prefixUrl($this->instagram),
            'site'      => $this->prefixUrl($this->site),
            'events'    => $this->prepareEvents($this->events),
        ]);
    }

    private function prefixUrl(?string $value): ?string
    {
        if (empty($value)) {
            return $value;
        }

        // Se já começa com http:// ou https://, retorna como está
        if (preg_match('/^https?:\/\//i', $value)) {
            return $value;
        }

        return 'https://' . $value;
    }

    private function prepareEvents(?array $events): ?array
    {
        if (empty($events)) {
            return $events;
        }

        foreach ($events as $i => $event) {
            $events[$i]['url'] = $this->prefixUrl($event['url'] ?? null);
        }

        return $events;
    }

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
            'cities' => [
                'required',
                'array',
            ],
            'cities.*' => [
                'required',
                'int',
                'exists:cities,id',
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
                'required',
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