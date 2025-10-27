<?php

namespace App\Http\Controllers\Modules\Admin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Muestra la vista de login del staff.
     */
    public function create(): View
    {
        // Usaremos una vista que crearemos en:
        // resources/views/admin/auth/login.blade.php
        return view('admin.auth.login');
    }

    /**
     * Maneja el intento de autenticación del staff.
     */
    public function store(Request $request): RedirectResponse
    {

        $request->merge(['email' => strtolower($request->input('email'))]);

        // 1. Validar los datos de entrada
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        // 2. Intentar autenticar usando el guard 'staff'
        $credentials = $request->only('email', 'password');

        if (!Auth::guard('staff')->attempt($credentials, $request->boolean('remember'))) {
            // 3. Si falla, regresa con error
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        // 4. Si tiene éxito, regenera la sesión y redirige al dashboard de admin
        $request->session()->regenerate();

        return redirect()->intended(route('admin.dashboard'));
    }

    /**
     * Cierra la sesión del staff.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('staff')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect(route('admin.login'));
    }
}
