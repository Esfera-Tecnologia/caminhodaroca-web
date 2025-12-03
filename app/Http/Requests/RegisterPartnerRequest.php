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
            // Normalize the field that the UI uses: externalLink
            $normalized = $this->prefixUrl($event['externalLink'] ?? null);

            // Ensure validation uses externalLink (user-visible field)
            $events[$i]['externalLink'] = $normalized;

            // Keep 'url' for backward compatibility if other code expects it.
            // Remove this line if you don't need the 'url' key anywhere else.
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
                new InstagramRule()
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
            'events.*.externalLink' => [
                'nullable',
                'string',
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
