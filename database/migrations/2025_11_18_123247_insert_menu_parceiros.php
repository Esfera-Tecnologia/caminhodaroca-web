<?php

use App\Models\AccessProfile;
use App\Models\Menu;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::beginTransaction();
        try {
            $menu = Menu::query()->firstOrCreate([
                'slug' => 'partners',
            ], [
                'nome' => 'Parceiros',
                'icone' => 'fas fa-users',
            ]);

            AccessProfile::query()->where('nome', 'Administrador de Sistema')->first()->permissions()
                ->firstOrCreate([
                    'menu_id' => $menu->id,
                ], [
                    'can_create' => true,
                    'can_read' => true,
                    'can_update' => true,
                    'can_delete' => true,
                ]);

            AccessProfile::query()->firstOrCreate([
                'nome' => 'Responsável'
            ], [
                'descricao' => 'Responsável das propriedades',
            ])->permissions()
                ->firstOrCreate([
                    'menu_id' => Menu::query()->firstOrCreate([
                        'slug' => 'properties'
                    ], [
                        'nome' => 'Propriedade',
                        'icone' => 'fas fa-tractor',
                    ])->id
                ], [
                    'can_create' => true,
                    'can_read' => true,
                    'can_update' => true,
                    'can_delete' => true,
                ]);

            AccessProfile::query()->firstOrCreate([
                'nome' => 'Parceiro'
            ], [
                'descricao' => 'Parceiro',
            ])->permissions()
                ->firstOrCreate([
                    'menu_id' => $menu->id,
                ], [
                    'can_create' => true,
                    'can_read' => true,
                    'can_update' => true,
                    'can_delete' => true,
                ]);
            DB::commit();
        }catch (\Exception $e){
            DB::rollBack();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
