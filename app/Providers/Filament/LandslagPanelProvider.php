<?php

namespace App\Providers\Filament;

use App\Filament\Landslag\Resources\TestResultsResource;
use App\Filament\Landslag\Widgets;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Hugomyb\FilamentErrorMailer\FilamentErrorMailerPlugin;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class LandslagPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('landslag')
            ->path('landslag')
            ->login()
            ->viteTheme('resources/css/filament/landslag/theme.css')
            ->discoverResources(in: app_path('Filament/Landslag/Resources'), for: 'App\\Filament\\Landslag\\Resources')
            ->discoverPages(in: app_path('Filament/Landslag/Pages'), for: 'App\\Filament\\Landslag\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->plugin(FilamentErrorMailerPlugin::make())
            ->discoverWidgets(in: app_path('Filament/Landslag/Widgets'), for: 'App\\Filament\\Landslag\\Widgets')
            ->widgets([
                TestResultsResource\Widgets\VektChart::class,
                TestResultsResource\Widgets\StyrkeChart::class,
                TestResultsResource\Widgets\RheitChart::class,
                Widgets\TreningsProgrammerTable::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->maxContentWidth('full')
            ->colors([
                'primary' => [
                    50 => 'rgb(239, 246, 255)',    // #eff6ff
                    100 => 'rgb(219, 234, 254)',   // #dbeafe
                    200 => 'rgb(191, 219, 254)',   // #bfdbfe
                    300 => 'rgb(147, 197, 253)',   // #93c5fd
                    400 => 'rgb(96, 165, 250)',    // #60a5fa
                    500 => 'rgb(59, 130, 246)',    // #3b82f6
                    600 => 'rgb(37, 99, 235)',     // #2563eb
                    700 => 'rgb(29, 78, 216)',     // #1d4ed8
                    800 => 'rgb(30, 64, 175)',     // #1e40af
                    900 => 'rgb(30, 58, 138)',     // #1e3a8a
                ],
            ])

            ->topNavigation();
    }
}
