<?php

namespace App\Providers\Filament;

use Althinect\FilamentSpatieRolesPermissions\FilamentSpatieRolesPermissionsPlugin;
use App\Filament\Widgets;
use App\Http\Middleware\IsAdmin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Navigation\NavigationItem;
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

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->profile()
            ->passwordReset()
            ->colors([
                'primary' => Color::Slate,
            ])
            ->navigationItems([
                NavigationItem::make('Til Uloba siden')
                    ->url('https://uloba.no', shouldOpenInNewTab: true)
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->activeIcon('heroicon-s-presentation-chart-line')
                    ->group('Eksterne Linker')
                    ->sort(7),
            ])
            ->navigationGroups([
                'Tider',
                'Medisinsk',
                'Diverse',
                'Landslag',
                'Authentication',
            ])
            ->userMenuItems([
                MenuItem::make()
                    ->label('Innstillinger')
                    ->url(fn() => route('filament.admin.pages.innstillinger'))
                    ->icon('heroicon-o-cog-6-tooth'),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                //Pages\Dashboard::class,
            ])
            ->discoverWidgets(in : app_path('Filament/Widgets'),
                              for: 'App\\Filament\\Widgets')//app_path('Filament/Widgets'), //'App\\Filament\\Widgets'*/
            ->widgets([
                Widgets\Ansatte::class,
                Widgets\AnsattKanIkkeJobbe::class,
                Widgets\BrukteTimerChart::class,
                Widgets\NesteArbeidstider::class,
                Widgets\TimerChart::class,
                Widgets\TimerIUka::class,
                Widgets\UserStats::class,
//                AccountWidget::class,
//                FilamentInfoWidget::class,
            ])
            ->plugin(FilamentSpatieRolesPermissionsPlugin::make())
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
                IsAdmin::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->maxContentWidth('full')
            //->sidebarFullyCollapsibleOnDesktop()
            ->topNavigation();
    }
}
