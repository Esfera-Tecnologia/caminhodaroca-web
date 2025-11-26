<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Factory as AuthFactory;
use Illuminate\Http\Request;
use Laravel\Sanctum\Guard;

class OptionalSanctumAuth
{
    public function __construct(
        protected AuthFactory $auth,
    ) {
    }

    public function handle(Request $request, Closure $next)
    {
        // Usa o guard sanctum normalmente; se não tiver token, segue sem usuário
        $user = $this->auth->guard('sanctum')->user();

        // Opcionalmente, define o usuário atual na request
        if ($user) {
            $request->setUserResolver(fn () => $user);
        }

        return $next($request);
    }
}
