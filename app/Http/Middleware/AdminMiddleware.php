<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'message' => 'Usuário não autenticado'
            ], 401);
        }

        // Verifica se o usuário tem perfil de administrador
        // Assumindo que existe um access_profile_id = 1 para administradores
        if ($user->access_profile_id !== 1) {
            return response()->json([
                'message' => 'Acesso negado. Privilégios de administrador necessários.'
            ], 403);
        }

        return $next($request);
    }
}
