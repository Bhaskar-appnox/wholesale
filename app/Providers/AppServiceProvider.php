<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        // Register API routes
        Route::prefix('api')
             ->middleware('api')
             ->group(base_path('routes/api.php'));
    }

    /**
     * Map the web routes.
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
             ->group(base_path('routes/web.php'));
    }
}
