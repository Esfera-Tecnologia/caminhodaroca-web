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
        Schema::table('partners', function (Blueprint $table) {
            $table->string('instagram')->nullable()->change();
            $table->string('site')->nullable()->change();
        });
        Schema::table('preapproved_partners', function (Blueprint $table) {
            $table->string('instagram')->nullable()->change();
            $table->string('site')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('partners', function (Blueprint $table) {
            $table->string('instagram')->nullable(false)->change();
            $table->string('site')->nullable(false)->change();
        });
        Schema::table('preapproved_partners', function (Blueprint $table) {
            $table->string('instagram')->nullable()->change();
            $table->string('site')->nullable()->change();
        });
    }
};
