<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

class PartnerController extends Controller
{
    public function index()
    {
        $partners =  collect(range(1, 10))->map(function ($i) {
            return [
                'id' => $i,
                'logo' => "https://picsum.photos/200/300",
                'name' => fake()->company(),
                'city' => fake()->city(),
                'state' => fake()->state(),
                'editable' => fake()->boolean(),
                'pendingApproval' => fake()->boolean(),
            ];
        });
        return response()->json($partners);
    }
}
