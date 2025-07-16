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
            $table->foreignId('access_profile_id')->after('password')->constrained()->onDelete('restrict');
            $table->enum('status', ['ativo', 'inativo'])->default('ativo')->after('access_profile_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([ 'access_profile_id', 'status']);
        });
    }
};
