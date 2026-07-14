<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 0. Drop if exists to recover from a previous failed migration attempt
        Schema::dropIfExists('partner_events');

        // 1. Create partner_events table
        Schema::create('partner_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('partner_id')->constrained('partners')->cascadeOnDelete();
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->string('url')->nullable();
            $table->string('status')->default('approved');
            $table->timestamps();
        });

        // 2. Copy data from events where start_date is null (which means they are partner events)
        DB::statement("
            INSERT INTO partner_events (id, partner_id, name, description, url, status, created_at, updated_at)
            SELECT id, partner_id, name, description, url, status, created_at, updated_at 
            FROM events 
            WHERE start_date IS NULL
        ");

        // 3. Clean up orphan records before applying new constraints
        DB::statement("DELETE FROM event_images WHERE event_id NOT IN (SELECT id FROM partner_events)");
        DB::statement("DELETE FROM preapproved_events WHERE event_id NOT IN (SELECT id FROM partner_events)");

        // 4. Drop existing foreign keys and constraints on event_images and preapproved_events
        try {
            Schema::table('event_images', function (Blueprint $table) {
                $table->dropForeign(['event_id']);
            });
        } catch (\Exception $e) {}

        Schema::table('event_images', function (Blueprint $table) {
            $table->foreign('event_id')->references('id')->on('partner_events')->cascadeOnDelete();
        });

        try {
            Schema::table('preapproved_events', function (Blueprint $table) {
                $table->dropForeign(['event_id']);
            });
        } catch (\Exception $e) {}

        Schema::table('preapproved_events', function (Blueprint $table) {
            $table->foreign('event_id')->references('id')->on('partner_events')->cascadeOnDelete();
        });

        // 5. Delete old records from events table to keep it clean for D11 Events
        // DB::statement("DELETE FROM events WHERE start_date IS NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Copy data back
        DB::statement("
            INSERT INTO events (id, partner_id, name, description, url, status, created_at, updated_at)
            SELECT id, partner_id, name, description, url, status, created_at, updated_at 
            FROM partner_events
        ");

        // 2. Restore foreign keys
        Schema::table('event_images', function (Blueprint $table) {
            $table->dropForeign(['event_id']);
            $table->foreign('event_id')->references('id')->on('events')->cascadeOnDelete();
        });

        Schema::table('preapproved_events', function (Blueprint $table) {
            $table->dropForeign(['event_id']);
            $table->foreign('event_id')->references('id')->on('events')->cascadeOnDelete();
        });

        // 3. Drop the partner_events table
        Schema::dropIfExists('partner_events');
    }
};
