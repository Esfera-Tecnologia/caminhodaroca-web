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

    public function show($id)
    {
        $partner = [
            'id' => (int) $id,
            'name' => fake()->company(),
            'logo' => "https://picsum.photos/seed/partner{$id}/200/300",
            'city' => fake()->city(),
            'uf' => fake()->stateAbbr(),
            'category' => fake()->randomElement(['Fazenda', 'Sítio', 'Pousada', 'Cabana']),
            'subcategory' => fake()->randomElement(['Ecológica', 'Histórica', 'Gastronômica', 'Aventura']),
            'description' => fake()->paragraph(3),
            'email' => fake()->companyEmail(),
            'routes' => fake()->sentence(6),
            'circuits' => fake()->sentence(6),
            'attractions' => fake()->sentence(6),
        ];

        return response()->json($partner);
    }
}
