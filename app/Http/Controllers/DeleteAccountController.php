<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DeleteAccountController extends Controller
{
    public function showDeleteForm()
    {
        return view('account.delete');
    }

    public function deleteAccount(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);
        try {
            $user = User::where('email', $request->email)->first();
            if (!$user || !Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
                return back()->withErrors(['email' => 'O e-mail ou a senha estão incorretos.']);
            }
            Auth::logout();
            $user->subcategories()->detach();
            $user->favoriteProperties()->detach();
            $user->delete();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect('/')->with('status', 'account-deleted');
        } catch (\Exception $e) {
            return back()->withErrors(['email' => 'Ocorreu um erro ao tentar excluir a conta. Por favor, tente novamente mais tarde.']);
        }
    }
}
