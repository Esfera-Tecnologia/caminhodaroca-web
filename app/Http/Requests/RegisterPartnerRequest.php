<?php

namespace App\Http\Requests;

use App\Models\PartnerCategory;
use App\Rules\InstagramRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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

        foreach ($events as $index => $event) {
            if (!is_array($event)) {
                unset($events[$index]);
                continue;
            }

            // Descarta eventos totalmente vazios (sem nome, descrição, link ou imagem)
            if ($this->isEventEmpty($event, $index)) {
                unset($events[$index]);
                continue;
            }

            // O link do evento pode vir como "url" (formulário web) ou "externalLink" (API/mobile).
            // Normaliza e mantém ambos sincronizados para não perder o valor digitado.
            $rawUrl = $event['externalLink'] ?? $event['url'] ?? null;
            $normalized = $this->prefixUrl($rawUrl);

            $events[$index]['externalLink'] = $normalized;
            $events[$index]['url'] = $normalized;
        }

        return $events ?: null;
    }

    /**
     * Verifica se um evento está totalmente vazio (nenhum campo e nenhuma imagem).
     */
    private function isEventEmpty(array $event, int $index): bool
    {
        foreach (['name', 'description', 'url', 'externalLink'] as $field) {
            if (!empty(trim((string) ($event[$field] ?? '')))) {
                return false;
            }
        }

        // Considera preenchido se houver alguma imagem enviada
        return !$this->hasFile("events.$index.images");
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
        $rules = [
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
                'max:1000',
            ],
            'circuits' => [
                'required',
                'string',
                'max:1000',
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

        if (!$this->is('api/register/partner', 'api/partners/*', 'cadastro-parceiro-publico')) {
            return $rules;
        }

        $categoryId = $this->input('partner_category_id');
        $category = is_numeric($categoryId)
            ? PartnerCategory::active()->find((int) $categoryId)
            : null;
        $experienceRule = $category?->experiencias_oferecidas ? 'required' : 'nullable';

        $rules['partner_category_id'] = [
            'required',
            'integer',
            Rule::exists('partner_categories', 'id')
                ->where(fn ($query) => $query->where('status', 'ativo')),
        ];
        $rules['routes'] = [$experienceRule, 'string', 'max:1000'];
        $rules['circuits'] = [$experienceRule, 'string', 'max:1000'];
        $rules['attractions'] = [$experienceRule, 'string', 'max:1000'];

        // Aceite dos Termos de Uso exigido apenas no cadastro público web
        if ($this->is('cadastro-parceiro-publico')) {
            $rules['terms'] = ['required', 'accepted'];
        }

        return $rules;
    }

    public function attributes(): array
    {
        return [
            'partner_category_id' => 'categoria',
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
            'terms' => 'termos de uso',
        ];
    }

    public function messages(): array
    {
        return [
            'terms.accepted' => 'Você deve aceitar os termos de uso para continuar.',
        ];
    }
}
