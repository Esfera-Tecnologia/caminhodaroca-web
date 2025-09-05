<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        // Para rotas de API, não redirecionar, apenas retornar null
        // O Laravel automaticamente retornará um 401 JSON response
        if ($request->expectsJson() || $request->is('api/*')) {
            return null;
        }

        return route('login');
    }

    /**
     * Handle an unauthenticated user.
     */
    protected function unauthenticated($request, array $guards)
    {
        // Para rotas de API, sempre retornar JSON
        if ($request->expectsJson() || $request->is('api/*')) {
            abort(response()->json([
                'message' => 'Usuário não autenticado.'
            ], 401));
        }

        // Para rotas web, usar comportamento padrão
        parent::unauthenticated($request, $guards);
    }
}
