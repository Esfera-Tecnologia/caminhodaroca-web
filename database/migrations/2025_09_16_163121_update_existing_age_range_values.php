<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Mapeamento dos valores antigos para os novos
        $mappings = [
            'FROM_30_TO_39' => 'FROM_30_TO_44',
            'FROM_40_TO_49' => 'FROM_45_TO_59',
            'FROM_50_TO_59' => 'ABOVE_60',
            'OVER_60' => 'ABOVE_60',
        ];

        foreach ($mappings as $oldValue => $newValue) {
            DB::table('users')
                ->where('age_range', $oldValue)
                ->update(['age_range' => $newValue]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverter os mapeamentos (apenas uma tentativa de reversão aproximada)
        $reverseMappings = [
            'FROM_30_TO_44' => 'FROM_30_TO_39',
            'FROM_45_TO_59' => 'FROM_40_TO_49',
            'ABOVE_60' => 'OVER_60',
        ];

        foreach ($reverseMappings as $newValue => $oldValue) {
            DB::table('users')
                ->where('age_range', $newValue)
                ->update(['age_range' => $oldValue]);
        }
    }
};
