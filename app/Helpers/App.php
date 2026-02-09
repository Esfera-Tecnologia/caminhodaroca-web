<?php

use App\Models\AccessProfileMenuPermission;
use App\Models\Menu;

if (!function_exists("getPermissao")) {
    function getPermissao(string $slug)
    {
        $menuId = Menu::where('slug', $slug)->value('id');

        $permissions = AccessProfileMenuPermission::query()
            ->whereIn('access_profile_id', auth()->user()->profiles->pluck('id'))
            ->get();

        if(! session()->has('permissions')) {
             session()->now('permissions', $permissions);
        }
        return session('permissions')->firstWhere('menu_id', $menuId);
    }
}