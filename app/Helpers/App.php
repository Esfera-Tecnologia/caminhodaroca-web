<?php

use App\Models\AccessProfileMenuPermission;
use App\Models\Menu;

if (!function_exists("getPermissao")) {
    function getPermissao(string $slug)
    {
        $user = auth()->user();

        if ($slug === 'properties' && $user->isResponsible()) {
            return (object) [
                'can_view'   => 1,
                'can_create' => 1,
                'can_edit'   => 1,
                'can_delete' => 1,
            ];
        }
        $menuId = Menu::where('slug', $slug)->value('id');

        $permissions = AccessProfileMenuPermission::query()
            ->whereIn('access_profile_id',  $user->profiles->pluck('id'))
            ->get();

        if(! session()->has('permissions')) {
             session()->now('permissions', $permissions);
        }
        return session('permissions')->firstWhere('menu_id', $menuId);
    }
}