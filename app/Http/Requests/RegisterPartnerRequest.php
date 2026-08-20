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

        // Mantém a logo mesmo quando o formulário volta de um erro (base64)
        if (!$this->hasFile('logo') && !empty($this->input('logo_base64'))) {
            $file = $this->base64ToUploadedFile((string) $this->input('logo_base64'));
            if ($file) {
                $this->files->set('logo', $file);
                $this->convertedFiles = null; // força a re-conversão do cache de arquivos
            }
        }
    }

    private function base64ToUploadedFile(string $dataUrl): ?\Illuminate\Http\UploadedFile
    {
        if (!str_starts_with($dataUrl, 'data:') || !str_contains($dataUrl, ',')) {
            return null;
        }

        [$meta, $base64] = explode(',', $dataUrl, 2);
        $decoded = base64_decode($base64, true);
        if ($decoded === false || $decoded === '') {
            return null;
        }

        $mime = '';
        if (preg_match('/^data:([^;]+);/', $meta, $m)) {
            $mime = $m[1];
        }

        $ext = 'png';
        $mimeMap = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif', 'image/webp' => 'webp'];
        if (isset($mimeMap[$mime])) {
            $ext = $mimeMap[$mime];
        }

        $tmp = tempnam(sys_get_temp_dir(), 'logo');
        file_put_contents($tmp, $decoded);

        return new \Illuminate\Http\UploadedFile($tmp, 'logo.' . $ext, $mime ?: null, UPLOAD_ERR_OK, true);
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
        ];
    }
}
