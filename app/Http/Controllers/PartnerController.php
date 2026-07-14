<?php

namespace App\Http\Controllers;

use App\Enums\PartnerStatus;
use App\Enums\PreapprovedPartnerStatus;
use App\Http\Requests\UpdatePartnerRequest;
use App\Models\Partner;
use App\Models\PartnerEvent;
use App\Models\PreapprovedPartner;
use App\Models\PreapprovedPartnerEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PartnerController extends Controller
{

    public function index()
    {
        $permissao = getPermissao('partners');
        abort_unless($permissao?->can_view, 403);

        $partners = Partner::query()->when(Auth::user()->isPartner(), function ($q) {
            $q->where('email', Auth::user()->email);
        })->latest()->get();
        return view('partners.index', compact('partners'));
    }

    public function edit(Partner $partner)
    {
        $permissao = getPermissao('partners');

        return view('partners.edit', compact('partner'));
    }

    public function update(UpdatePartnerRequest $request, Partner $partner)
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();
            if (isset($data['logo'])) {
                if (Storage::disk('public')->exists($partner->logo)) {
                    Storage::disk('public')->delete($partner->logo);
                }
                $data['logo'] = $request->file('logo')->store('partners', 'public');
            }
            $partner->cities()->sync($data['cities']);
            $existingEventIds = [];
            if (isset($data['events'])) {
                foreach ($data['events'] as $key => $eventData) {
                    $existingEventIds[] = $eventData['id'];
                    $event = PartnerEvent::find($eventData['id']);
                    $event->update($eventData);
                    if (isset($eventData['images'])) {
                        $eventData['images'] = $request->file('events')[$key]['images']->store('partners/events', 'public');
                        $event->images()->delete();
                        $event->images()->create(['image' => $eventData['images']]);
                    }
                }
            }

            if ($existingEventIds === []) {
                $partner->events()->delete();
            } else {
                $partner->events()->whereNotIn('id', $existingEventIds)->delete();
            }

            if (isset($data['new_event_name'])) {
                foreach ($data['new_event_name'] as $key => $eventName) {
                    $eventData = [
                        'name' => $eventName,
                        'url' => $data['new_event_link'][$key] ?? null,
                        'description' => $data['new_event_description'][$key] ?? null,
                    ];
                    $newEvent = $partner->events()->create($eventData);

                    if (isset($data['new_event_imagem'][$key])) {
                        $imagePath = $request->file('new_event_imagem')[$key]->store('partners/events', 'public');
                        $newEvent->images()->create(['image' => $imagePath]);
                    }
                }
            }
            $partner->update($data);
            DB::commit();
            return redirect()->route('partners.index')->with('success', 'Parceiro atualizado com sucesso!');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Falha ao atualizar parceiro', [
                'partner_id' => $partner->id,
                'exception' => $e,
            ]);
            return redirect()->route('partners.index')->with('error', 'Falha ao atualizar o parceiro!');
        }
    }

    public function destroy(Partner $partner)
    {
        DB::beginTransaction();
        try {
            $partner->delete();
            DB::commit();
            return redirect()->route('partners.index')->with('success', 'Parceiro excluído com sucesso!');
        }catch (\Exception $e){
            DB::rollBack();
            Log::error($e->getMessage(),  $e->getTrace());
            return redirect()->route('partners.index')->with('error', 'Falha ao excluir parceiro!');
        }
    }

    public function edit_public(PreapprovedPartner $partner)
    {
        $permissao = getPermissao('partners');
        abort_unless($permissao?->can_edit, 403);

        return view('partners.edit', compact('partner'));
    }

    public function update_public(Request $request, PreapprovedPartner $partner)
    {
        DB::beginTransaction();
        try {
            $data = $request->all();
            if (isset($data['logo'])) {
                if (Storage::disk('public')->exists($partner->logo)) {
                    Storage::disk('public')->delete($partner->logo);
                }
                $data['logo'] = $request->file('logo')->store('partners', 'public');
            }
            $partner->cities()->sync($data['cities']);
            if (isset($data['events'])) {
                foreach ($data['events'] as $key => $eventData) {
                    $event = PreapprovedPartnerEvent::find($eventData['id']);
                    $event->update($eventData);
                    if (isset($eventData['images'])) {
                        $eventData['images'] = $request->file('events')[$key]['images']->store('partners/events', 'public');
                        $event->images()->delete();
                        $event->images()->create(['image' => $eventData['images']]);
                    }
                }
                $partner->events()->whereNotIn('id', array_map(function ($row){
                    return $row['id'];
                }, $data['events']))->delete();
            }
            if (isset($data['new_event_name'])) {
                foreach ($data['new_event_name'] as $key => $eventName) {
                    $eventData = [
                        'name' => $eventName,
                        'url' => $data['new_event_link'][$key] ?? null,
                        'description' => $data['new_event_description'][$key] ?? null,
                    ];
                    $newEvent = $partner->events()->create($eventData);
                    
                    if (isset($data['new_event_imagem'][$key])) {
                        $imagePath = $request->file('new_event_imagem')[$key]->store('partners/events', 'public');
                        $newEvent->images()->create(['image' => $imagePath]);
                    }
                }
            }
            if (($request->has('approve_updates') && $request->input('approve_updates')) || ($partner->status != $request->input('status'))) {
                $data['status'] = PreapprovedPartnerStatus::APPROVED;
                $dataPartner = $data;
                $dataPartner['status'] = PartnerStatus::ATIVO;
                $dataPartner['approved'] = 1;
                unset($dataPartner['_token']);
                unset($dataPartner['_method']);
                $this->syncPartnerEventsFromPreapproved($partner);
                $partner->partner->cities()->sync($data['cities']);
                unset($dataPartner['cities']);
                unset($dataPartner['events']);
                unset($dataPartner['approve_updates']);
                $partner->partner()->update($dataPartner);
            }
            $partner->update($data);
            DB::commit();
            return redirect()->route('partners.index')->with('success', 'Parceiro atualizado com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage(), $e->getTrace());
            return redirect()->route('partners.index')->with('error', 'Falha ao atualizar o parceiro!');
        }
    }

    private function syncPartnerEventsFromPreapproved(PreapprovedPartner $preapprovedPartner): void
    {
        // obtém o parceiro definitivo vinculado
        /** @var Partner $partner */
        $partner = $preapprovedPartner->partner()->first();
        if (!$partner) {
            return;
        }

        // 1) Excluir todos os eventos atuais do parceiro
        $partner->events()->whereNotIn('id', $preapprovedPartner->events->pluck('event_id'))->delete();

        // 2) Buscar os eventos do parceiro pré-aprovado
        $preapprovedEvents = $preapprovedPartner->events;

        // 3) Criar novos eventos para o parceiro definitivo
        foreach ($preapprovedEvents as $preEvent) {
            if($preEvent->event_id) {
                $event = $partner->events()->find($preEvent->event_id);
                $event->update([
                    'name' => $preEvent->name,
                    'description' => $preEvent->description,
                    'url' => $preEvent->url,
                    'status' => 'approved'
                ]);
                if(isset($preEvent->images()->first()?->image)) {
                    $image = $event->images()->firstOrNew();
                    $image->image = $preEvent->images()->first()->image;
                    $image->save();
                }
            } else {
                // ajuste os campos conforme os atributos existentes em PartnerEvent
                $event = $partner->events()->updateOrCreate([
                    'name' => $preEvent->name,
                    'description' => $preEvent->description,
                    'url' => $preEvent->url,
                    'status' => 'approved'
                ]);
                if(isset($preEvent->images()->first()?->image)) {
                    $event->images()->create(['image' => $preEvent->images()->first()?->image??null]);
                }
            }
        }
    }
}
