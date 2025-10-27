<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Str; // Importante

class Authenticate extends Middleware
{
    protected function redirectTo(Request $request): ?string
    {
        if (! $request->expectsJson()) {

            // Si la ruta empieza con /admin
            if (Str::startsWith($request->path(), 'admin')) {
                // Redirígelo al login de admin
                return route('admin.login');
            }

            // De lo contrario, al login de usuario normal
            return route('login');
        }

        return null;
    }
}
