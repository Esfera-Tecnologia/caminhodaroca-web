<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('whatsapp');
            $table->enum('status', ['ativo', 'inativo'])->default('ativo');
            $table->string('instagram')->nullable();
            $table->string('endereco_principal');
            $table->string('endereco_secundario')->nullable();
            $table->string('cidade');
            $table->string('descricao_servico', 1000);
            $table->tinyInteger('certificacao')->nullable()->change();
            $table->boolean('vende_produtos_artesanais')->default(false);
            $table->json('produtos_artesanais')->nullable();
            $table->enum('tipo_funcionamento', ['todos', 'fins', 'feriados', 'agendamento', 'personalizado'])->default('todos');
            $table->text('observacoes_funcionamento')->nullable();
            $table->json('agenda_personalizada')->nullable();
            $table->boolean('aceita_animais')->default(false);
            $table->boolean('possui_acessibilidade')->default(false);
            $table->string('logo_path')->nullable();
            $table->json('galeria_paths')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
