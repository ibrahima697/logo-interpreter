<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\InterpreteurLogo;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
        $this->app->singletonIf(InterpreteurLogo::class, function ($app) {
            return new InterpreteurLogo();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
