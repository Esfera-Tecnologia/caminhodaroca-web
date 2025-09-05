<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiResponseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Se a resposta for de erro de validação, formatamos conforme a especificação
        if ($response->getStatusCode() === 422 && $request->expectsJson()) {
            $content = json_decode($response->getContent(), true);
            
            if (isset($content['errors'])) {
                $formattedResponse = [
                    'message' => $content['message'] ?? 'Os dados informados são inválidos.',
                    'errors' => $content['errors']
                ];
                
                return response()->json($formattedResponse, 422);
            }
        }

        return $response;
    }
}
