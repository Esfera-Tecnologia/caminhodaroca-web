<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class PropertyController extends Controller
{
    /**
     * Mapeia dias da semana português para inglês
     */
    private const DAY_MAPPING = [
        'segunda' => 'monday',
        'terça' => 'tuesday',
        'quarta' => 'wednesday',
        'quinta' => 'thursday',
        'sexta' => 'friday',
        'sábado' => 'saturday',
        'domingo' => 'sunday'
    ];

    /**
     * Estrutura padrão de horários
     */
    private const DEFAULT_OPENING_HOURS = [
        'monday'    => ['open' => null, 'close' => null, 'lunchBreak' => false],
        'tuesday'   => ['open' => null, 'close' => null, 'lunchBreak' => false],
        'wednesday' => ['open' => null, 'close' => null, 'lunchBreak' => false],
        'thursday'  => ['open' => null, 'close' => null, 'lunchBreak' => false],
        'friday'    => ['open' => null, 'close' => null, 'lunchBreak' => false],
        'saturday'  => ['open' => null, 'close' => null, 'lunchBreak' => false],
        'sunday'    => ['open' => null, 'close' => null, 'lunchBreak' => false],
        'custom'    => ''
    ];

    public function index(): JsonResponse
    {
        $query = Property::with(['categorias', 'subcategories', 'images', 'products', 'ratings']);

        if ($keyword = request()->query('keyword')) {
            $query->where(function($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                  ->orWhere('descricao_servico', 'like', "%{$keyword}%");
            });
        }

        if ($categories = request()->query('categories')) {
            // Garantir que é array
            if (!is_array($categories)) {
                $categories = is_string($categories) ? explode(',', $categories) : [$categories];
            }
            // Filtrar e converter para inteiros
            $categories = array_filter(array_map('intval', $categories));
            
            if (!empty($categories)) {
                $query->whereHas('categorias', function($q) use ($categories) {
                    $q->whereIn('categories.id', $categories);
                });
            }
        }

        if ($subcategories = request()->query('subcategories')) {
            // Garantir que é array
            if (!is_array($subcategories)) {
                $subcategories = is_string($subcategories) ? explode(',', $subcategories) : [$subcategories];
            }
            // Filtrar e converter para inteiros
            $subcategories = array_filter(array_map('intval', $subcategories));
            
            if (!empty($subcategories)) {
                $query->whereHas('subcategories', function($q) use ($subcategories) {
                    $q->whereIn('subcategories.id', $subcategories);
                });
            }
        }

        // Filtro por localização(sujeito a mudanças pois tem que enviar nome da cidade e nao o ID)
        if ($propertyLocationId = request()->query('propertyLocationId')) {
            $query->where('cidade', 'like', "%{$propertyLocationId}%");
        }

        // Filtro por favoritos (requer autenticação)
        if (request()->query('isFavorite') === 'true' || request()->query('isFavorite') === true) {
            $user = request()->user() ?? Auth::guard('sanctum')->user();
            
            if (!$user) {
                return response()->json([
                    'message' => 'Usuário não autenticado'
                ], 401);
            }
            
            $query->whereHas('favoritedByUsers', function($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }

        $properties = $query->get();

        if ($properties->isEmpty()) {
        return response()->json([], 200);
    }

        $properties = $properties->map(function($property) {
            return [
                'id' => $property->id,
                'name' => $property->name,
                'logo' => $property->logo,
                'type' => $property->type ?? 'Propriedade Rural',
                'rating' => round($property->average_rating, 1),
                'location' => [
                    'city' => $property->city ?? 'Cidade não informada',
                    'coordinates' => [
                        'lat' => $property->latitude ?? 0,
                        'lng' => $property->longitude ?? 0,
                    ]
                ],
            ];
        });

        return response()->json($properties);
    }

    public function show(int $id): JsonResponse
    {
        $property = Property::with(['categorias', 'subcategories', 'images', 'products', 'ratings'])->find($id);

        if (!$property) {
            return response()->json([
                'message' => 'Propriedade não encontrada'
            ], 404);
        }

        // Verifica se o usuário está logado e se a propriedade está favoritada
        $user = request()->user() ?? Auth::guard('sanctum')->user();
        $isFavorited = $user?->favoriteProperties()->where('property_id', $id)->exists() ?? false;
        

        return response()->json([
            'id' => $property->id,
            'name' => $property->name,
            'logo' => $property->logo,
            'phone' => $property->phone,
            'rating' => round($property->average_rating, 1),
            'type' => $property->type ?? 'Propriedade Rural',
            'link_google_maps' => $property->google_maps_url ?? '',
            'isFavorited' => $isFavorited,
            'address' => $this->formatAddress($property),
            'instagram' => $property->instagram ?? '',
            'location' => [
                'city' => $property->city ?? 'Cidade não informada',
                'state' => $property->state ?? 'Rio de Janeiro',
                'coordinates' => [
                    'lat' => $property->latitude ?? 0,
                    'lng' => $property->longitude ?? 0,
                ]
            ],
            'description' => $property->description ?? 'Descrição não disponível',
            'category' => $this->getCommaSeparatedNames($property->categorias, 'Categoria não informada'),
            'subcategory' => $this->getCommaSeparatedNames($property->subcategories, 'Subcategoria não informada'),
            'openingHours' => $this->formatOpeningHours($property),
            'products' => $this->getProductNames($property->products),
            'accessibility' => $property->accessibility ?? 'Informações de acessibilidade não disponíveis',
            'petPolicy' => $property->pet_policy ?? 'Política para animais não informada',
            'gallery' => $this->getGallery($property),
        ]);
    }

    public function toggleFavorite(int $id): JsonResponse
    {
        $user = request()->user();
        
        if (!$user) {
            return response()->json([
                'message' => 'Usuário não autenticado'
            ], 401);
        }

        $property = Property::find($id);

        if (!$property) {
            return response()->json([
                'message' => 'Propriedade não encontrada'
            ], 404);
        }

        // Verifica se já está favoritado
        $isFavorited = $user->favoriteProperties()->where('property_id', $id)->exists();

        if ($isFavorited) {
            $user->favoriteProperties()->detach($id);
            return response()->json([
                'favorited' => false,
                'message' => 'Propriedade removida dos favoritos'
            ]);
        } else {
            $user->favoriteProperties()->attach($id);
            return response()->json([
                'favorited' => true,
                'message' => 'Propriedade adicionada aos favoritos'
            ]);
        }
    }

    /**
     * Formata o endereço da propriedade
     */
    private function formatAddress(Property $property): string
    {
        $enderecoPrincipal = $property->endereco_principal ?? '';
        
        if (empty($enderecoPrincipal)) {
            return 'Endereço não informado';
        }
        
        return $enderecoPrincipal;
    }

    /**
     * Obtém nomes separados por vírgula sem duplicatas
     */
    private function getCommaSeparatedNames($collection, string $default = 'Não informado'): string
    {
        return $collection->isNotEmpty() 
            ? $collection->pluck('name')->unique()->implode(', ')
            : $default;
    }

    /**
     * Obtém nomes de produtos separados por vírgula
     */
    private function getProductNames($products): string
    {
        return $products->isNotEmpty() 
            ? $products->pluck('nome')->unique()->implode(', ')
            : 'Produtos não informados';
    }

    /**
     * Obtém galeria de imagens da propriedade
     */
    private function getGallery(Property $property): array
    {
        $gallery = $property->images->pluck('image')->toArray();
        
        if (empty($gallery)) {
            return [
                'https://picsum.photos/200/300',
                'https://picsum.photos/200/300',
                'https://picsum.photos/200/300',
            ];
        }

        return $gallery;
    }

    /**
     * Formata horários de funcionamento conforme tipo de funcionamento
     */
    private function formatOpeningHours(Property $property): array
    {
        $openingHours = self::DEFAULT_OPENING_HOURS;
        $tipoFuncionamento = $property->tipo_funcionamento ?? 'todos';

        switch ($tipoFuncionamento) {
            case 'agendamento':
                return $this->formatAgendamentoHours($property, $openingHours);
            
            case 'personalizado':
                return $this->formatPersonalizadoHours($property, $openingHours);
            
            case 'fins':
                return $this->formatFinsHours($property, $openingHours);
            
            case 'todos':
            default:
                return $this->formatTodosHours($property, $openingHours);
        }
    }

    /**
     * Formata horários para tipo 'agendamento'
     */
    private function formatAgendamentoHours(Property $property, array $openingHours): array
    {
        $openingHours['custom'] = $property->observacoes_funcionamento ?? 'Abertura apenas sob agendamento com a equipe';
        return $openingHours;
    }

    /**
     * Formata horários para tipo 'personalizado'
     */
    private function formatPersonalizadoHours(Property $property, array $openingHours): array
    {
        $openingHours['custom'] = $property->observacoes_funcionamento ?? 'Horário personalizado - consulte a propriedade';
        return $openingHours;
    }

    /**
     * Formata horários para tipo 'fins' (apenas finais de semana)
     */
    private function formatFinsHours(Property $property, array $openingHours): array
    {
        $this->applyScheduleFromJson($property, $openingHours, ['saturday', 'sunday']);
        $openingHours['custom'] = 'Funcionamento apenas nos finais de semana';
        return $openingHours;
    }

    /**
     * Formata horários para tipo 'todos' (todos os dias)
     */
    private function formatTodosHours(Property $property, array $openingHours): array
    {
        $this->applyScheduleFromJson($property, $openingHours);
        $openingHours['custom'] = $property->observacoes_funcionamento ?? '';
        return $openingHours;
    }

    /**
     * Aplica horários do JSON da agenda personalizada
     */
    private function applyScheduleFromJson(Property $property, array &$openingHours, array $allowedDays = null): void
    {
        if (!$property->agenda_personalizada) {
            return;
        }

        $agenda = is_string($property->agenda_personalizada) 
            ? json_decode($property->agenda_personalizada, true) 
            : $property->agenda_personalizada;

        if (!is_array($agenda)) {
            return;
        }

        foreach ($agenda as $diaPt => $dados) {
            $diaEn = self::DAY_MAPPING[$diaPt] ?? null;
            
            if (!$diaEn || !isset($dados['ativo'])) {
                continue;
            }

            // Se allowedDays está definido, aplicar apenas para esses dias
            if ($allowedDays !== null && !in_array($diaEn, $allowedDays)) {
                continue;
            }

            if ($dados['ativo'] == '1' || $dados['ativo'] === true) {
                $openingHours[$diaEn] = [
                    'open' => $dados['abertura'] ?? null,
                    'close' => $dados['fechamento'] ?? null,
                    'lunchBreak' => ($dados['fecha_almoco'] == '1' || $dados['fecha_almoco'] === true)
                ];
            }
        }
    }
}
