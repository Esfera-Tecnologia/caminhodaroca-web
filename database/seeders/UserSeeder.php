<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'denys@esfera.com.br'],
            [
                'name' => 'Denys Rodrigues',
                'email' => 'denys@esfera.com.br',
                'password' => Hash::make('123456'),
                'access_profile_id' => 1,
            ]
        );
    }
}
