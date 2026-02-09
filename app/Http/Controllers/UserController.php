<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Menu;
use App\Models\AccessProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Notifications\WelcomeNewUserNotification;

class UserController extends Controller
{

    public function index()
    {
        $permissao = getPermissao('users');

        abort_unless($permissao?->can_view, 403);

        $users = User::query()
            ->where('registration_source', 'web')
            ->with('profiles')
            ->get();

        return view('users.index', compact('users'));
    }

    public function create()
    {
        $permissao = getPermissao('users');

        abort_unless($permissao?->can_create, 403);

        $accessProfiles = AccessProfile::where('status', 'ativo')->orderBy('nome')->get();

        $user = new User();

        return view('users.create', compact('user', 'accessProfiles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:191',
            'email' => 'required|email|unique:users,email',
            'access_profile_id' => 'required|array|min:1',
            'access_profile_id.*' => 'required|integer|exists:access_profiles,id',
            'status' => 'required|in:ativo,inativo',
            'can_approve_property' => 'required|in:1,0',
        ]);

        $user = new User($request->except('password', 'access_profile_id'));
        $user->password = Hash::make($request->password);
        $user->registration_source = 'web';
        $user->save();
        $user->profiles()->sync($request->access_profile_id);
        $user->notify(new WelcomeNewUserNotification($user));

        return redirect()->route('users.index')->with('success', 'Usuário cadastrado com sucesso.');
    }

    public function edit(User $user)
    {

       $permissao = getPermissao('users');
        abort_unless($permissao?->can_edit, 403);

        $accessProfiles = AccessProfile::where('status', 'ativo')->orderBy('nome')->get();

        return view('users.edit', compact('user', 'accessProfiles'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:191',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'access_profile_id' => 'required|array|min:1',
            'access_profile_id.*' => 'required|integer|exists:access_profiles,id',
            'status' => 'required|in:ativo,inativo',
            'can_approve_property' => 'required|in:1,0',
        ]);
        $user->fill($request->except('password', 'access_profile_id'));
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        $user->save();
        $user->profiles()->sync($request->access_profile_id);

        return redirect()->route('users.index')->with('success', 'Usuário atualizado com sucesso.');
    }

    public function destroy(User $user)
    {
        $permissao = getPermissao('users');
        abort_unless($permissao?->can_delete, 403);

        $user->delete();

        return redirect()->route('users.index')->with('success', 'Usuário excluído com sucesso.');
    }
}
