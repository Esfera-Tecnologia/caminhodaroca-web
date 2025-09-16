<?php

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
        Schema::table('users', function (Blueprint $table) {
            // Change travel_with from enum to string
            $table->string('travel_with')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Revert back to enum
            $table->enum('travel_with', ['ALONE', 'COUPLE', 'FAMILY', 'FRIENDS', 'GROUPS'])->nullable()->change();
        });
    }
};
