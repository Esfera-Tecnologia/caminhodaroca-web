<?php

namespace App\Http\Controllers\Api;

use App\Enums\PreapprovedPartnerStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdatePartnerRequest;
use App\Http\Resources\PartnerResource;
use App\Http\Resources\PreapprovedPartnerResource;
use App\Models\Partner;
use App\Models\PartnerEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PartnerController extends Controller
{
    public function index(Request $request)
    {
        $user = request()->user();

        try {
            $partners = PartnerResource::collection(Partner::query()
                ->cities($request->cities ?? [])
                ->keyword($request->keyword ?? null)
                ->get());
            return response()->json($partners);
        } catch (\Exception $exception) {
            Log::info($exception);
            return response()->json(['status' => false, 'message' => 'Não foi possível buscar as informações'], 500);
        }
    }

    public function show(Partner $id)
    {
        $id->individual = true;
        $user = request()->user();
        $userPartners = $user?->partner->pluck('id')->toArray()??[];
        $pendingData = $id->preapproved_partner()->first();
        $canEdit = in_array($id->id, $userPartners);

        if ($canEdit && $pendingData && $pendingData->status == PreapprovedPartnerStatus::PENDING) {
            $partner = PreapprovedPartnerResource::make($pendingData);
        } else {
            $partner = PartnerResource::make($id);
        }
        return response()->json($partner);
    }

    public function update(UpdatePartnerRequest $request, Partner $id)
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();
            if (isset($data['logo'])) {
                $data['logo'] = $request->file('logo')->store('partners', 'public');
            }
            $preapproved_partner = $id->preapproved_partner()->first();
            $preapproved_partner->update(array_merge($data, [
                'status' => PreapprovedPartnerStatus::PENDING
            ]));
            $preapproved_partner->cities()->sync($data['cities']);
            if (isset($data['events'])) {
                $eventsIds = collect($data['events'])->pluck('eventId');
                $preapproved_partner->events()
                    ->whereNull('event_id')
                    ->delete();
                $id->events()
                   ->whereNotIn('id', $eventsIds)
                   ->delete();
                foreach ($data['events'] as $eventData) {
                    $imageData = [];
                    if(isset($eventData['images'])) {
                        $imageData['image'] = $eventData['images'][0]->store('partners/events', 'public');
                    }
                    if (isset($eventData['eventId'])) {
                        $event = PartnerEvent::find($eventData['eventId']);
                        $preapproved = $event->preapproved_event()->first();
                        $preapproved->update($eventData);
                        if ($imageData) {
                            $eventImage = $preapproved->images()->firstOrNew();
                            $eventImage->image = $imageData['image'];
                            $eventImage->save();
                        }
                    } else {
                        $event = $id->events()->create(array_merge($eventData, [
                            'status' => 'pending'
                        ]));
                        $preapproved = $preapproved_partner->events()->create(array_merge($eventData, [
                            'event_id' => $event->id,
                        ]));
                        if ($imageData) {
                            $event->images()->create($imageData);
                            $preapproved->images()->create($imageData);
                        }
                    }
                }
            }
            DB::commit();
            return response()->json(['message' => "O parceiro foi atualizado com sucesso!"]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage(), $e->getTrace());
            return response()->json(['message' => "Não foi possível atualizar o parceiro!"], 500);
        }

    }
}
