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
        Schema::table('events', function (Blueprint $table) {
            $table->dateTime('start_date')->nullable()->after('description');
            $table->dateTime('end_date')->nullable()->after('start_date');
            $table->foreignId('state_id')->nullable()->after('end_date')->constrained('states');
            $table->foreignId('city_id')->nullable()->after('state_id')->constrained('cities');
            $table->string('organization')->nullable()->after('city_id');
            $table->text('full_description')->nullable()->after('organization');
            $table->string('image')->nullable()->after('full_description');
            $table->boolean('is_highlight')->default(false)->after('image');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropForeign(['state_id']);
            $table->dropForeign(['city_id']);
            $table->dropColumn([
                'start_date',
                'end_date',
                'state_id',
                'city_id',
                'organization',
                'full_description',
                'image',
                'is_highlight'
            ]);
        });
    }
};
