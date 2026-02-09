<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_has_access_profile', function (Blueprint $table) {
            $table->foreignId('user_id')
                ->constrained('users', 'id')
                ->cascadeOnDelete();
            $table->foreignId('access_profile_id')
                ->constrained('access_profiles', 'id')
                ->cascadeOnDelete();
        });
        User::all()->map(function($user){
            $user->profiles()->attach($user->access_profile_id);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_has_access_profile');
    }
};
