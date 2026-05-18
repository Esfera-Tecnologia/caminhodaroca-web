<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Event;
use App\Models\Partner;
use App\Models\Property;
use App\Models\State;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    public function index()
    {
        $permissao = getPermissao('events');
        abort_unless($permissao?->can_view, 403);

        $events = Event::with(['state', 'city', 'properties'])->latest()->get();
        return view('events.index', compact('events'));
    }

    public function create()
    {
        $permissao = getPermissao('events');
        abort_unless($permissao?->can_create, 403);

        $partners = Partner::where('status', 'ativo')->orderBy('name')->get();
        $properties = Property::where('status', 'ativo')->orderBy('name')->get();
        $states = State::orderBy('name')->get();
        
        $stateId = old('state_id');
        $cities = $stateId ? City::where('state_id', $stateId)->orderBy('name')->get() : collect();
        
        return view('events.create', compact('partners', 'properties', 'states', 'cities'));
    }

    public function store(Request $request)
    {
        $permissao = getPermissao('events');
        abort_unless($permissao?->can_create, 403);

        $data = $this->validateEvent($request);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('events', 'public');
        }

        $data['status'] = 'approved';
        $data['is_highlight'] = $request->has('is_highlight');

        $event = Event::create($data);

        if ($request->has('property_ids')) {
            $event->properties()->sync($request->property_ids);
        }

        return redirect()->route('events.index')->with('success', 'Evento cadastrado com sucesso!');
    }

    public function edit(Event $event)
    {
        $permissao = getPermissao('events');
        abort_unless($permissao?->can_edit, 403);

        $partners = Partner::where('status', 'ativo')->orderBy('name')->get();
        $properties = Property::where('status', 'ativo')->orderBy('name')->get();
        $states = State::orderBy('name')->get();
        
        $stateId = old('state_id', $event->state_id);
        $cities = $stateId ? City::where('state_id', $stateId)->orderBy('name')->get() : collect();
        
        $selectedProperties = $event->properties->pluck('id')->toArray();

        return view('events.edit', compact('event', 'partners', 'properties', 'states', 'cities', 'selectedProperties'));
    }

    public function update(Request $request, Event $event)
    {
        $permissao = getPermissao('events');
        abort_unless($permissao?->can_edit, 403);

        $data = $this->validateEvent($request, $event->id);

        if ($request->hasFile('image')) {
            if ($event->image) {
                Storage::disk('public')->delete($event->image);
            }
            $data['image'] = $request->file('image')->store('events', 'public');
        }

        $data['is_highlight'] = $request->has('is_highlight');

        $event->update($data);

        if ($request->has('property_ids')) {
            $event->properties()->sync($request->property_ids);
        } else {
            $event->properties()->detach();
        }

        return redirect()->route('events.index')->with('success', 'Evento atualizado com sucesso!');
    }

    public function destroy(Event $event)
    {
        $permissao = getPermissao('events');
        abort_unless($permissao?->can_delete, 403);

        if ($event->image) {
            Storage::disk('public')->delete($event->image);
        }

        $event->properties()->detach();
        $event->delete();

        return redirect()->route('events.index')->with('success', 'Evento excluído com sucesso!');
    }

    protected function validateEvent(Request $request, $id = null)
    {
        if ($request->has('url') && !empty($request->input('url'))) {
            $url = $request->input('url');
            if (!preg_match('/^https?:\/\//i', $url)) {
                $request->merge(['url' => 'https://' . $url]);
            }
        }

        return $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'state_id' => 'required|exists:states,id',
            'city_id' => 'required|exists:cities,id',
            'full_description' => 'required|string',
            'image' => ($id ? 'nullable' : 'required') . '|image|mimes:jpg,jpeg,png|max:5120',
            'partner_id' => 'required|exists:partners,id',
            'organization' => 'nullable|string|max:255',
            'url' => 'nullable|url|max:255',
            'property_ids' => 'nullable|array',
            'property_ids.*' => 'exists:properties,id',
        ], [
            'name.required' => 'O título é obrigatório.',
            'description.required' => 'A descrição breve é obrigatória.',
            'start_date.required' => 'A data inicial é obrigatória.',
            'end_date.required' => 'A data final é obrigatória.',
            'state_id.required' => 'O estado é obrigatório.',
            'city_id.required' => 'A cidade é obrigatória.',
            'full_description.required' => 'O campo "Sobre o Evento" é obrigatório.',
            'image.required' => 'A foto de capa é obrigatória.',
            'partner_id.required' => 'O parceiro vinculado é obrigatório.',
        ]);
    }
}
