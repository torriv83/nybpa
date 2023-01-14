<?php

namespace App\Providers;

use Filament\Facades\Filament;
use Filament\Navigation\NavigationItem;
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
        Filament::serving(function () {
            Filament::registerNavigationItems([
                NavigationItem::make('Til Uloba siden')
                    ->url('https://uloba.no', shouldOpenInNewTab: true)
                    ->icon('heroicon-o-external-link')
                    ->activeIcon('heroicon-s-presentation-chart-line')
                    ->group('Eksterne linker')
                    ->sort(3),
            ]);
        });
    }
}
