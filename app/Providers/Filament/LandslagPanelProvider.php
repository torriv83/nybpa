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
use Filament\Support\Colors\Color;
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
            ->colors([
                'primary' => Color::Green,
            ])
            ->discoverResources(in: app_path('Filament/Landslag/Resources'), for: 'App\\Filament\\Landslag\\Resources')
            ->discoverPages(in: app_path('Filament/Landslag/Pages'), for: 'App\\Filament\\Landslag\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Landslag/Widgets'), for: 'App\\Filament\\Landslag\\Widgets')
            ->widgets([
                TestResultsResource\Widgets\VektChart::class,
                TestResultsResource\Widgets\StyrkeChart::class,
                TestResultsResource\Widgets\RheitChart::class,
                Widgets\TreningsProgrammerTable::class,
//                Widgets\AccountWidget::class,
//                Widgets\FilamentInfoWidget::class,
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
            ->topNavigation();
    }
}
