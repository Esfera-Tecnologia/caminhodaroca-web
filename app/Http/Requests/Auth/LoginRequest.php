<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'password' => ['required'],
        ];
    }

    

    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        // Tenta autenticar normalmente
        if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        // Recupera o usuário autenticado
        $user = Auth::user();

        // Verifica se usuário criado via API está tentando fazer login via web
        if ($user->registration_source === 'api') {
            Auth::logout();

            throw ValidationException::withMessages([
                'email' => 'Este usuário não tem permissão para acessar via web. Utilize o aplicativo móvel.',
            ]);
        }

        // Verifica se o usuário está inativo
        if ($user->status !== 'ativo') {
            Auth::logout();

            throw ValidationException::withMessages([
                'email' => 'Usuário inativo. Entre em contato com o administrador.',
            ]);
        }

        // Verifica se o perfil de acesso está inativo
        if ($user->accessProfile && $user->accessProfile->status !== 'ativo') {
            Auth::logout();

            throw ValidationException::withMessages([
                'email' => 'O perfil de acesso vinculado ao usuário está inativo. Contate o administrador.',
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }




    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    public function throttleKey(): string
    {
        return Str::lower($this->input('email')) . '|' . $this->ip();
    }
}
