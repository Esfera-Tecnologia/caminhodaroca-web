<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('partner_categories', function (Blueprint $table) {
            $table->id();
            $table->string('titulo', 191)->unique();
            $table->enum('status', ['ativo', 'inativo'])->default('ativo');
            $table->boolean('experiencias_oferecidas')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('partner_categories');
    }
};
