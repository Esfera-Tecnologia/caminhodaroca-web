<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('category_property_subcategories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->foreignId('subcategory_id')->nullable()->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->unique(['property_id', 'category_id', 'subcategory_id'], 'property_category_subcategory_unique');
            
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('category_property_subcategories');
    }
};
