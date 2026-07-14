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
        $query = Event::with(['city', 'state:id,code', 'properties'])
            ->where('status', 'approved');

        // Filtro de Destaques (Home)
        if ($request->has('is_highlight')) {
            $query->where('is_highlight', $request->boolean('is_highlight'));
            if ($request->boolean('is_highlight')) {
                $query->where('end_date', '>=', now());
            }
        }

        // Filtro por Data Específica (Calendário)
        if ($request->has('date')) {
            $query->whereDate('start_date', '<=', $request->date)
                  ->whereDate('end_date', '>=', $request->date);
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
            $query->where('start_date', '>=', now()->startOfDay())
                  ->where('start_date', '<=', now()->addDays(7));
            $query->orderBy('start_date', 'asc');
        } elseif ($request->filter === 'expired') {
            // Já aconteceram
            $query->where('end_date', '<', now()->endOfDay());
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
        $event = Event::with(['city', 'state:id,name', 'properties'])->findOrFail($id);
        return new EventResource($event);
    }

    /**
     * Retorna estatísticas para o calendário (quais dias tem eventos)
     */
    public function calendarStats(Request $request)
    {
        $month = $request->month ?? now()->month;
        $year = $request->year ?? now()->year;

        $startDate = \Carbon\Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = \Carbon\Carbon::createFromDate($year, $month, 1)->endOfMonth();

        $events = Event::where('status', 'approved')
            ->where(function($q) use ($startDate, $endDate) {
                $q->whereDate('start_date', '<=', $endDate->format('Y-m-d'))
                  ->whereDate('end_date', '>=', $startDate->format('Y-m-d'));
            })
            ->get();

        $stats = [];

        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $dateStr = $date->format('Y-m-d');
            
            $activeEvents = $events->filter(function($event) use ($dateStr) {
                $eventStart = \Carbon\Carbon::parse($event->start_date)->format('Y-m-d');
                $eventEnd = \Carbon\Carbon::parse($event->end_date)->format('Y-m-d');
                return $eventStart <= $dateStr && $eventEnd >= $dateStr;
            });
            
            $count = $activeEvents->count();
            if ($count > 0) {
                $stats[] = [
                    'date' => $dateStr,
                    'count' => $count,
                    'single_event_id' => $count === 1 ? (int) $activeEvents->first()->id : null
                ];
            }
        }

        return response()->json($stats);
    }
}
