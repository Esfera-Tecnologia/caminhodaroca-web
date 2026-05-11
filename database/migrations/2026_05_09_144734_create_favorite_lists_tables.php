<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Criar tabela de listas de favoritos
        Schema::create('favorite_lists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->boolean('is_default')->default(false);
            $table->timestamps();

            $table->unique(['user_id', 'name']);
        });

        // 2. Criar tabela pivô entre listas e propriedades
        Schema::create('favorite_list_properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('favorite_list_id')->constrained()->onDelete('cascade');
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->unique(['favorite_list_id', 'property_id']);
        });

        // 3. Migração de dados: Criar lista "Favoritos" para TODOS os usuários
        $users = DB::table('users')->select('id')->get();
        foreach ($users as $user) {
            $listId = DB::table('favorite_lists')->insertGetId([
                'user_id' => $user->id,
                'name' => 'Favoritos',
                'is_default' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 4. Mover favoritos antigos para a nova lista "Favoritos" deste usuário
            $oldFavorites = DB::table('user_favorite_properties')
                ->where('user_id', $user->id)
                ->get();

            foreach ($oldFavorites as $fav) {
                DB::table('favorite_list_properties')->insert([
                    'favorite_list_id' => $listId,
                    'property_id' => $fav->property_id,
                    'created_at' => $fav->created_at ?? now(),
                    'updated_at' => $fav->updated_at ?? now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('favorite_list_properties');
        Schema::dropIfExists('favorite_lists');
    }
};
