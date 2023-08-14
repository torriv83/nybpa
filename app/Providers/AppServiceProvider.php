<?php

namespace App\Providers;

use Filament\Support\Facades\FilamentAsset;
use Illuminate\Foundation\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        FilamentAsset::registerScriptData([
            // asset('build/assets/custom.css'),
            app(Vite::class)('resources/css/custom.scss'),
        ]);

    }
}
