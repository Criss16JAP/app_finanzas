<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;
use App\Modules\Core\Account; // 👈 Importamos el modelo correcto

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Define tu namespace base para los controladores.
     * (opcional en Laravel 9+, pero útil si usas rutas agrupadas)
     */
    protected $namespace = 'App\\Http\\Controllers';

    /**
     * This namespace is applied to your controller routes.
     *
     * @var string|null
     */
    public const HOME = '/home';

    /**
     * Define las rutas del sistema.
     */
    public function boot(): void
    {
        parent::boot();

        Route::model('account', Account::class);
        Route::model('transaction', \App\Modules\Core\Transaction::class);


        // Si luego tienes más modelos modulares, los registras así:
        // Route::model('transaction', \App\Modules\Core\Transaction::class);
        // Route::model('user', \App\Modules\Users\User::class);

        /**
         * Aquí definimos los archivos de rutas del proyecto.
         */
        $this->routes(function () {
            Route::middleware('web')
                ->namespace($this->namespace)
                ->group(base_path('routes/web.php'));

            Route::middleware('api')
                ->prefix('api')
                ->namespace($this->namespace)
                ->group(base_path('routes/api.php'));
        });
    }
}
