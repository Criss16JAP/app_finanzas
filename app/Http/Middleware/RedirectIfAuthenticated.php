<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {

                // 1. Si el guard es 'staff' y está logueado
                if ($guard === 'staff') {
                    // Redirígelo al dashboard de admin
                    return redirect(route('admin.dashboard'));
                }

                // 2. De lo contrario, es un usuario 'web'
                return redirect(route('dashboard'));
            }
        }

        return $next($request);
    }
}
