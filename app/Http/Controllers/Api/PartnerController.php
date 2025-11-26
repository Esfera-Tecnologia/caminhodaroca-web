<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdatePartnerRequest;
use App\Http\Resources\PartnerResource;
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
            return response()->json(['status' => false, 'message' => 'Não foi possível buscar as informações'], 500);
        }
    }

    public function show(Partner $id)
    {
        $id->individual = true;

        $partner = PartnerResource::make($id);

        return response()->json($partner);
    }

    public function update(UpdatePartnerRequest $request, Partner $id)
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();
            if (isset($data['logo'])) {
                if (Storage::disk('public')->exists($id->logo)) {
                    Storage::disk('public')->delete($id->logo);
                }
                $data['logo'] = $request->file('logo')->store('partners', 'public');
            }
            $preapproved_partner = $id->preapproved_partner()->first();
            $preapproved_partner->update($data);
            $preapproved_partner->cities()->sync($data['cities']);
            if (isset($data['events'])) {
                foreach ($data['events'] as $eventData) {
                    if (isset($eventData['id'])) {
                        $event = PartnerEvent::find($eventData['id']);
                        $event->preapproved_event()->first()->update($eventData);
                    } else {
                        $preapproved_partner->events()->create($eventData);
                    }
                }
            }
            DB::commit();
            return response()->json(['message' => "O parceiro foi atualizado com sucesso!"]);
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);
            Log::error($e->getMessage(), $e->getTrace());
            return response()->json(['message' => "Não foi possível atualizar o parceiro!"], 500);
        }

    }
}
