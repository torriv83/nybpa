<?php

namespace App\Providers;

use App\Models\User;
use BezhanSalleh\PanelSwitch\PanelSwitch;
use Filament\Forms\Components\DateTimePicker;
use Filament\Support\Assets\Css;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        /* BACKUP */
        Gate::define('download-backup', function (User $user) {
            return $user->hasRole('Admin');
        });

        Gate::define('delete-backup', function (User $user) {
            return $user->hasRole('Admin');
        });

        /* Filament Assets */
        FilamentAsset::register([
            Css::make('custom-stylesheet', __DIR__.'/../../resources/css/custom.css'),
        ]);

        /* PanelSwitcher */
        PanelSwitch::configureUsing(function (PanelSwitch $panelSwitch) {
            $panelSwitch
                // ->simple()
                ->visible(fn (): bool => auth()->user()?->hasRole('Admin'))
                ->renderHook('panels::user-menu.after');
        });

        /* Global Settings */
        DateTimePicker::configureUsing(function (DateTimePicker $dateTimePicker): void {
            $dateTimePicker
                ->displayFormat('d.m.Y H:i');
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
