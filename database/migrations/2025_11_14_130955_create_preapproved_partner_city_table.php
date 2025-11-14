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
        Schema::create('preapproved_partner_city', function (Blueprint $table) {
            $table->foreignId('preapproved_partner_id')->constrained()->on('preapproved_partners')->cascadeOnDelete();
            $table->foreignId('city_id')->constrained()->on('cities')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('preapproved_partner_city');
    }
};
