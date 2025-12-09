<?php

namespace Database\Factories;

use App\Models\Partner;
use App\Models\PartnerEvent;
use Illuminate\Database\Eloquent\Factories\Factory;

class PartnerEventFactory extends Factory
{
    protected $model = PartnerEvent::class;

    public function definition(): array
    {
        return [
            'partner_id'  => Partner::factory(),
            'name'        => $this->faker->catchPhrase(),
            'description' => $this->faker->paragraph(),
            'url'         => $this->faker->url(),
        ];
    }
}
