<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Primeiro, vamos converter os dados existentes para JSON
        $users = DB::table('users')->whereNotNull('travel_with')->get();
        
        foreach ($users as $user) {
            if (!empty($user->travel_with)) {
                // Converte string única para array JSON
                $travelWithArray = [$user->travel_with];
                DB::table('users')
                    ->where('id', $user->id)
                    ->update(['travel_with' => json_encode($travelWithArray)]);
            }
        }
        
        // Agora altera o tipo da coluna para JSON
        Schema::table('users', function (Blueprint $table) {
            $table->json('travel_with')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Primeiro converte de volta para string
        $users = DB::table('users')->whereNotNull('travel_with')->get();
        
        foreach ($users as $user) {
            if (!empty($user->travel_with)) {
                $travelWithArray = json_decode($user->travel_with, true);
                if (is_array($travelWithArray) && !empty($travelWithArray)) {
                    // Pega o primeiro valor do array
                    $firstValue = $travelWithArray[0];
                    DB::table('users')
                        ->where('id', $user->id)
                        ->update(['travel_with' => $firstValue]);
                }
            }
        }
        
        // Altera de volta para string
        Schema::table('users', function (Blueprint $table) {
            $table->string('travel_with', 50)->nullable()->change();
        });
    }
};
