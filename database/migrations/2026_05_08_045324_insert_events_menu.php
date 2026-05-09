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
        DB::beginTransaction();
        try {
            $menu = \App\Models\Menu::query()->updateOrCreate([
                'slug' => 'events',
            ], [
                'nome' => 'Eventos',
                'icone' => 'fas fa-calendar-alt',
                'ordem' => 0,
            ]);

            $adminProfile = \App\Models\AccessProfile::query()->where('nome', 'Administrador de Sistema')->first();
            
            if ($adminProfile) {
                $adminProfile->permissions()->updateOrCreate([
                    'menu_id' => $menu->id,
                ], [
                    'can_view'   => true,
                    'can_create' => true,
                    'can_edit'   => true,
                    'can_delete' => true,
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $menu = \App\Models\Menu::where('slug', 'events')->first();
        if ($menu) {
            DB::table('profile_menu_permissions')->where('menu_id', $menu->id)->delete();
            $menu->delete();
        }
    }
};
