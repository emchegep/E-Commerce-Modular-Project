<?php

namespace Modules\Product\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as
    BaseRouteServiceProvider;

class RouteServiceProvider extends BaseRouteServiceProvider
{
    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->routes(function (): void{
            Route::middleware('web')
                ->group(__DIR__.'/../routes.php');
        });
    }
}
