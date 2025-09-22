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
          'password' => [
              'required',
              'string',
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

      $user = User::where('email', $request->email)->firstOrFail();

      $user->password = Hash::make($request->password);
      $user->save();

      auth()->login($user);

      return redirect()->route('dashboard')->with('success', 'Senha definida com sucesso!');
  }

     public function formNovaSenha(Request $request, $token)
    {
        $email = $request->query('email');

        return view('auth.set-password', [
            'email' => $email,
            'token' => $token
        ]);
    }

}
