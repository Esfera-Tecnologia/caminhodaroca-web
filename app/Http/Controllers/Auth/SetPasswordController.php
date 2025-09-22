<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;

class SetPasswordController extends Controller
{
    public function showSetPasswordForm(Request $request)
    {
        return view('auth.set-password', ['email' => $request->email]);
    }

    public function storePassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => [
                'required',
                'min:8',
                'confirmed',
                function ($attribute, $value, $fail) {
                    $hasUpper = preg_match('/[A-Z]/', $value);
                    $hasLower = preg_match('/[a-z]/', $value);
                    $hasNumber = preg_match('/[0-9]/', $value);
                    $hasSpecial = preg_match('/[!@#$%&*()_+\-=\[\]{};\':"\\|,.<>\/?]/', $value);
                    
                    $typesCount = $hasUpper + $hasLower + $hasNumber + $hasSpecial;
                    
                    if ($typesCount < 2) {
                        $fail('A senha deve conter pelo menos 2 dos seguintes tipos de caracteres: letras maiúsculas (A-Z), letras minúsculas (a-z), números (0-9) ou caracteres especiais (!@#$%&*, etc.).');
                    }
                }
            ],
        ]);

        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->route('login')->with('success', 'Senha definida com sucesso!');
    }
}
