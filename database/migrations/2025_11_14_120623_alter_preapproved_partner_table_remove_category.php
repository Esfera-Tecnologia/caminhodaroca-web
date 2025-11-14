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
        Schema::table('preapproved_partners', function (Blueprint $table) {
            $table->dropConstrainedForeignId('category_id');
            $table->dropConstrainedForeignId('subcategory_id');
            $table->dropColumn('city');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('preapproved_partners', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()->after('user_id')->constrained()->on('categories')->cascadeOnDelete();
            $table->foreignId('subcategory_id')->nullable()->after('category_id')->constrained()->on('categories')->cascadeOnDelete();
            $table->string('city')->nullable()->after('site');
        });
    }
};
