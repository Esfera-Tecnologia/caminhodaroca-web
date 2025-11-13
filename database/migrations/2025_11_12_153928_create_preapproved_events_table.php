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
        Schema::create('preapproved_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('preapproved_partner_id')->constrained()->on('preapproved_partners')->cascadeOnDelete();
            $table->string('name');
            $table->text('description');
            $table->string('url');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('preapproved_events');
    }
};
