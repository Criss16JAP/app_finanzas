<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Importa el controlador de login de Admin
use App\Http\Controllers\Modules\Admin\Auth\AuthenticatedSessionController as AdminLoginController;

Route::get('/', function () {
    return view('welcome');
});

// Rutas de Usuario (Breeze)
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// --- RUTAS DEL PANEL DE ADMINISTRACIÓN (STAFF) ---

Route::prefix('admin')->name('admin.')->group(function () {

    // Ruta para mostrar el formulario de login (GET)
    Route::get('/login', [AdminLoginController::class, 'create'])
        ->middleware('guest:staff') // Solo invitados del guard 'staff'
        ->name('login');

    // Ruta para procesar el login (POST)
    Route::post('/login', [AdminLoginController::class, 'store'])
        ->middleware('guest:staff');

    // Ruta para hacer logout (POST)
    Route::post('/logout', [AdminLoginController::class, 'destroy'])
        ->middleware('auth:staff') // Solo autenticados del guard 'staff'
        ->name('logout');

    // Dashboard del Admin (Ejemplo)
    Route::get('/dashboard', function () {
        return view('admin.auth.dashboard');
    })->middleware('auth:staff')->name('dashboard');

});

// Las rutas de Breeze se quedan al final
require __DIR__ . '/auth.php';
