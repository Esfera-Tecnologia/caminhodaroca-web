<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Support\Facades\Hash;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class SenhaController extends Controller
{
  public function storeNovaSenha(Request $request)
{
    $request->validate([
        'email' => 'required|email|exists:users,email',
        'password' => 'required|string|min:8|confirmed',
    ]);

    $user = User::where('email', $request->email)->firstOrFail();

    $user->password = Hash::make($request->password);
    $user->save();

    auth()->login($user);

    return redirect()->route('dashboard')->with('success', 'Senha definida com sucesso!');
}
}
