<?php

namespace Database\Factories;

use App\Models\Partner;
use App\Models\User;
use App\Enums\PartnerStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

class PartnerFactory extends Factory
{
    protected $model = Partner::class;

    public function definition(): array
    {
        return [
            'user_id'     => User::inRandomOrder()->first()->id,
            'name'        => $this->faker->company(),
            'email'       => $this->faker->unique()->companyEmail(),
            'description' => $this->faker->sentence(10),
            'logo'        => 'logos/' . $this->faker->image('storage/app/public/logos', 640, 480, null, false),
            'instagram'   => 'https://instagram.com/' . $this->faker->userName(),
            'site'        => $this->faker->url(),
            'routes'      => $this->faker->words(3, true),
            'circuits'    => $this->faker->words(3, true),
            'attractions' => $this->faker->words(5, true),
            'status'      => $this->faker->randomElement(PartnerStatus::cases()),
        ];
    }
}
