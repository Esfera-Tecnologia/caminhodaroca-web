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
        Schema::table('preapproved_events', function (Blueprint $table) {
            $table->foreignId('event_id')->nullable()->after('preapproved_partner_id')->constrained()->on('events')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('preapproved_events', function (Blueprint $table) {
            $table->dropColumn('event_id');
        });
    }
};
