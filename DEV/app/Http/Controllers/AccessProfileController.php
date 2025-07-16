<?php

namespace App\Http\Controllers;

use App\Models\AccessProfile;
use App\Models\Menu;
use App\Models\AccessProfileMenuPermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AccessProfileController extends Controller
{
    public function index()
    {
       
        $permissao = $this->getPermissao('access-profiles');
        abort_unless($permissao?->can_view, 403);

        $accessProfiles = AccessProfile::all();
        return view('access_profiles.index', compact('accessProfiles'));
    }

    public function create()
    {
        $permissao = $this->getPermissao('access-profiles');
        abort_unless($permissao?->can_create, 403);

        $menus = Menu::all();
        return view('access_profiles.create', compact('menus'));
    }

    public function store(Request $request)
    {

        $request->validate([
            'nome' => 'required|string|unique:access_profiles,nome' . ($accessProfile->id ?? ''),
            'descricao' => 'required|string',
            'status' => 'required|in:ativo,inativo',
        ]);

        
        DB::transaction(function () use ($request) {
            $accessProfile = AccessProfile::create($request->only('nome', 'descricao', 'status'));

            foreach ($request->permissions ?? [] as $menuId => $perms) {
                AccessProfileMenuPermission::create([
                    'access_profile_id' => $accessProfile->id,
                    'menu_id' => $menuId,
                    'can_view' => isset($perms['view']),
                    'can_create' => isset($perms['create']),
                    'can_edit' => isset($perms['edit']),
                    'can_delete' => isset($perms['delete']),
                ]);
            }
        });

        return redirect()->route('access-profiles.index')->with('success', 'Perfil cadastrado com sucesso.');
    }

    public function edit(AccessProfile $accessProfile)
    {
               
        $permissao = $this->getPermissao('access-profiles');
         abort_unless($permissao?->can_edit, 403);

        $menus = Menu::all();

        $permissions = $accessProfile->permissions->keyBy('menu_id');
        return view('access_profiles.edit', compact('accessProfile', 'menus', 'permissions'));
    }

    public function update(Request $request, AccessProfile $accessProfile)
    {
        
        $request->validate([
            'nome' => ['required', 'string', Rule::unique('access_profiles')->ignore($accessProfile->id ?? null)],
            'descricao' => 'required|string',
            'status' => 'required|in:ativo,inativo',
        ]);

        DB::transaction(function () use ($request, $accessProfile) {
            $accessProfile->update($request->only('nome', 'descricao', 'status'));

            $accessProfile->permissions()->delete();

            foreach ($request->permissions ?? [] as $menuId => $perms) {
                AccessProfileMenuPermission::create([
                    'access_profile_id' => $accessProfile->id,
                    'menu_id' => $menuId,
                    'can_view' => isset($perms['view']),
                    'can_create' => isset($perms['create']),
                    'can_edit' => isset($perms['edit']),
                    'can_delete' => isset($perms['delete']),
                ]);
            }
        });

        return redirect()->route('access-profiles.index')->with('success', 'Perfil atualizado com sucesso.');
    }

    public function destroy(AccessProfile $accessProfile)
    {
        $permissao = $this->getPermissao('access-profiles');
        abort_unless($permissao?->can_delete, 403);
        
        // Verifica se existe algum usuário usando esse perfil
        if ($accessProfile->users()->count() > 0) {
            return redirect()->route('access-profiles.index')
                ->with('error', 'Este perfil está vinculado a um ou mais usuários e não pode ser excluído.');
        }

        $accessProfile->delete();

        return redirect()->route('access-profiles.index')
            ->with('success', 'Perfil excluído com sucesso.');
    }

    private function getPermissao(string $slug)
    {
        $menuId = Menu::where('slug', $slug)->value('id');

        return auth()->user()
            ->accessProfile
            ->permissions
            ->firstWhere('menu_id', $menuId);
    }

}
