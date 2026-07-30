<?php

use App\Models\AccessProfile;
use App\Models\AccessProfileMenuPermission;
use App\Models\Menu;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::beginTransaction();
        try {
            // 1. Criar o item no menu
            $menu = Menu::firstOrCreate([
                'slug' => 'partner-categories',
            ], [
                'nome' => 'Categorias de Parceiros',
                'icone' => 'fas fa-tags',
            ]);

            // 2. Atribuir permissões ao perfil "Administrador de Sistema"
            $adminProfile = AccessProfile::where('nome', 'Administrador de Sistema')->first();
            if ($adminProfile) {
                $adminProfile->permissions()
                    ->updateOrCreate([
                        'menu_id' => $menu->id,
                    ], [
                        'can_view'   => true,
                        'can_create' => true,
                        'can_edit'   => true,
                        'can_delete' => true,
                    ]);
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function down(): void
    {
        DB::beginTransaction();
        try {
            $menu = Menu::where('slug', 'partner-categories')->first();
            if ($menu) {
                AccessProfileMenuPermission::where('menu_id', $menu->id)->delete();
                $menu->delete();
            }
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
};
