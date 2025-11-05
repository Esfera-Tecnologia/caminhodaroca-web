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
        Schema::create('preapproved_properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->nullable()->references('id')->on('properties')->onDelete('cascade');
            $table->string('name');
            $table->string('whatsapp');
            $table->string('instagram')->nullable();
            $table->string('nome_responsavel');
            $table->string('email_responsavel');
            $table->string('endereco_principal');
            $table->string('endereco_secundario')->nullable();
            $table->text('google_maps_url')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('cidade');
            $table->string('descricao_servico', 1000);
            $table->tinyInteger('certificacao')->nullable();
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

        Schema::create('preapproved_property_product', function (Blueprint $table) {
            $table->id();

            $table->foreignId('preapproved_property_id')->references('id')->on('preapproved_properties')->onDelete('cascade');
            $table->foreignId('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('preapproved_property_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('preapproved_property_id')->references('id')->on('preapproved_properties')->onDelete('cascade');
            $table->string('path');
            $table->timestamps();
        });

        Schema::create('category_preapproved_property_subcategories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('preapproved_property_id')
                ->constrained('preapproved_properties')
                ->onDelete('cascade')
                ->name('fk_preapproved_property');

            $table->foreignId('category_id')
                ->constrained()
                ->onDelete('cascade')
                ->name('fk_category');

            $table->foreignId('subcategory_id')
                ->nullable()
                ->constrained()
                ->onDelete('cascade')
                ->name('fk_subcategory'); // Nome curto para a FK

            $table->timestamps();

            $table->unique(['preapproved_property_id', 'category_id', 'subcategory_id'], 'property_category_subcategory_unique');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('preapproved_property_product');
        Schema::dropIfExists('preapproved_property_images');
        Schema::dropIfExists('category_preapproved_property_subcategories');
        Schema::dropIfExists('preapproved_properties');
    }
};
