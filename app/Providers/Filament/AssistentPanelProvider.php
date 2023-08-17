<?php

namespace App\Providers\Filament;

use App\Filament\Assistent\Pages\Auth\EditProfile;
use App\Filament\Assistent\Widgets as AssistentWidget;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationItem;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AssistentPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('assistent')
            ->path('assistent')
            ->colors([
                'primary' => Color::Amber,
            ])
            ->login()
            ->profile(EditProfile::class)
            ->passwordReset()
            ->navigationItems([
                NavigationItem::make('Til Uloba siden')
                    ->url('https://uloba.no', shouldOpenInNewTab: true)
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->activeIcon('heroicon-s-presentation-chart-line')
                    ->group('Eksterne Linker')
                    ->sort(7),
            ])
            ->discoverResources(in: app_path('Filament/Assistent/Resources'), for: 'App\\Filament\\Assistent\\Resources')
            ->discoverPages(in: app_path('Filament/Assistent/Pages'), for: 'App\\Filament\\Assistent\\Pages')
            ->pages([
                //Pages\Dashboard::class,
            ])
            //->discoverWidgets(in: app_path('Filament/Assistent/Widgets'), for: 'App\\Filament\\Assistent\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                AssistentWidget\TotalWorked::class,
                AssistentWidget\UpcomingWorkTabell::class,
                AssistentWidget\TimeTabell::class,
                //Widgets\FilamentInfoWidget::class,
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
            ]);
    }
}
