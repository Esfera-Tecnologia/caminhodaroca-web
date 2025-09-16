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
        // Force change the age_range column to varchar with sufficient length
        DB::statement('ALTER TABLE users MODIFY COLUMN age_range VARCHAR(255) NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to enum (this is approximate, data may be lost)
        DB::statement("ALTER TABLE users MODIFY COLUMN age_range ENUM('UNDER_18', 'FROM_18_TO_29', 'FROM_30_TO_44', 'FROM_45_TO_59', 'ABOVE_60') NULL");
    }
};
