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
            $table->string('state', 2)->nullable()->after('email');
            $table->enum('age_range', ['FROM_18_TO_29', 'FROM_30_TO_39', 'FROM_40_TO_49', 'FROM_50_TO_59', 'OVER_60'])->nullable()->after('state');
            $table->enum('travel_with', ['ALONE', 'COUPLE', 'FAMILY', 'FRIENDS', 'GROUPS'])->nullable()->after('age_range');
            $table->foreignId('category_id')->nullable()->constrained()->after('travel_with');
            $table->string('avatar')->nullable()->after('category_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn(['state', 'age_range', 'travel_with', 'category_id', 'avatar']);
        });
    }
};
