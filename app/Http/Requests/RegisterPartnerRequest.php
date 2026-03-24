<?php

namespace App\Http\Requests;

use App\Rules\InstagramRule;
use App\Rules\URLRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class RegisterPartnerRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'site'      => $this->prefixUrl($this->site),
            'events'    => $this->prepareEvents($this->events),
        ]);
    }

    private function prefixUrl(?string $value): ?string
    {
        if (empty($value)) {
            return $value;
        }
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
            $normalized = $this->prefixUrl($event['externalLink'] ?? null);

            $events[$i]['externalLink'] = $normalized;

            $events[$i]['url'] = $normalized;
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
                'max:255',
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:partners,email',
            ],
            'description' => [
                'required',
                'string',
                'max:255',
            ],
            'logo' => [
                'required',
                'file',
                'max:2048',
            ],
            'instagram' => [
                'nullable',
                'string',
                'max:255',
                new InstagramRule()
            ],
            'site' => [
                'nullable',
                'string',
                'max:255',
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
                'max:255',
            ],
            'circuits' => [
                'required',
                'string',
                'max:255',
            ],
            'attractions' => [
                'required',
                'string',
                'max:1000',
            ],
            'events' => [
                'nullable',
                'array',
            ],
            'events.*.name' => [
                'required',
                'string',
                'max:255',
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
                'max:255',
                'url'
            ],
            'events.*.externalLink' => [
                'nullable',
                'string',
                'max:255',
                'url'
            ],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'nome',
            'email' => 'e-mail',
            'description' => 'descrição',
            'logo' => 'logo',
            'instagram' => 'instagram',
            'site' => 'site',
            'cities' => 'cidades',
            'cities.*' => 'cidade',
            'routes' => 'rotas',
            'circuits' => 'circuitos',
            'attractions' => 'atrações',

            'events' => 'eventos',
            'events.*.name' => 'nome do evento',
            'events.*.description' => 'descrição do evento',
            'events.*.images' => 'imagens do evento',
            'events.*.images.*' => 'imagem do evento',
            'events.*.url' => 'link externo do evento',
            'events.*.externalLink' => 'link externo do evento',
        ];
    }
}
