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
            $table->string('attractions', 1000)->change();
        });

        Schema::table('preapproved_partners', function (Blueprint $table) {
            $table->string('attractions', 1000)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('partners', function (Blueprint $table) {
            $table->string('attractions', 255)->change();
        });

        Schema::table('preapproved_partners', function (Blueprint $table) {
            $table->string('attractions', 255)->change();
        });
    }
};
