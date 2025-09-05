<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\State;
use App\Models\City;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function states(): JsonResponse
    {
        $states = State::orderBy('name')->get()->map(function ($state) {
            return [
                'value' => $state->code,
                'label' => $state->name
            ];
        });

        return response()->json($states);
    }

    public function cities(Request $request): JsonResponse
    {
        $query = City::with('state');

        // Filter by state if provided
        if ($request->has('uf') && $request->uf) {
            $query->whereHas('state', function ($q) use ($request) {
                $q->where('code', strtoupper($request->uf));
            });
        }

        $cities = $query->orderBy('name')->get()->map(function ($city) {
            return [
                'value' => $city->id,
                'label' => $city->name
            ];
        });

        return response()->json($cities);
    }
}
