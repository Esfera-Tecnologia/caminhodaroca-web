<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\EventResource;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EventController extends Controller
{
    /**
     * Lista eventos com filtros (Destaque, Próximos, Expirados, Busca)
     */
    public function index(Request $request)
    {
        $query = Event::with(['city', 'state', 'properties'])
            ->where('status', 'approved');

        // Filtro de Destaques (Home)
        if ($request->has('is_highlight')) {
            $query->where('is_highlight', $request->boolean('is_highlight'));
        }

        // Filtro por Data Específica (Calendário)
        if ($request->has('date')) {
            $query->whereDate('start_date', $request->date);
        }

        // Busca por Nome ou Local (Cidade/Estado)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhereHas('city', function($cq) use ($search) {
                      $cq->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('state', function($sq) use ($search) {
                      $sq->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
                  });
            });
        }

        // Filtros de Categoria de Tempo (Próximos / Expirados)
        if ($request->filter === 'upcoming') {
            // Próximos 7 dias a partir de hoje
            $query->where('start_date', '>=', now())
                  ->where('start_date', '<=', now()->addDays(7));
            $query->orderBy('start_date', 'asc');
        } elseif ($request->filter === 'expired') {
            // Já aconteceram
            $query->where('end_date', '<', now());
            $query->orderBy('start_date', 'desc');
        } else {
            // Ordenação padrão: mais próximos primeiro
            $query->orderBy('start_date', 'asc');
        }

        return EventResource::collection($query->get());
    }

    /**
     * Retorna detalhes de um evento específico
     */
    public function show($id)
    {
        $event = Event::with(['city', 'state', 'properties'])->findOrFail($id);
        return new EventResource($event);
    }

    /**
     * Retorna estatísticas para o calendário (quais dias tem eventos)
     */
    public function calendarStats(Request $request)
    {
        $month = $request->month ?? now()->month;
        $year = $request->year ?? now()->year;

        $stats = Event::select(
                DB::raw('DATE(start_date) as date'), 
                DB::raw('count(*) as count'), 
                DB::raw('MAX(id) as single_event_id')
            )
            ->whereYear('start_date', $year)
            ->whereMonth('start_date', $month)
            ->where('status', 'approved')
            ->groupBy('date')
            ->get();

        return response()->json($stats->map(function($stat) {
            return [
                'date' => $stat->date,
                'count' => (int) $stat->count,
                'single_event_id' => $stat->count == 1 ? (int) $stat->single_event_id : null
            ];
        }));
    }
}
