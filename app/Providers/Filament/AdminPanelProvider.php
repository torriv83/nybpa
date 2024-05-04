<?php

namespace App\Providers\Filament;

use Althinect\FilamentSpatieRolesPermissions\FilamentSpatieRolesPermissionsPlugin;
use App\Filament\Admin\Widgets;
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
use RickDBCN\FilamentEmail\FilamentEmail;
use Saade\FilamentFullCalendar\FilamentFullCalendarPlugin;
use ShuvroRoy\FilamentSpatieLaravelBackup\FilamentSpatieLaravelBackupPlugin;

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
            ->brandLogo(asset('images/logo.png'))
            ->brandLogoHeight('2.5rem')
            ->navigationItems([
                NavigationItem::make('Til Uloba siden')
                    ->url('https://uloba.no', shouldOpenInNewTab: true)
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->activeIcon('heroicon-s-presentation-chart-line')
                    ->group('Eksterne Linker')
                    ->sort(6),
                NavigationItem::make('Pulse')
                    ->url('/pulse', shouldOpenInNewTab: true)
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->activeIcon('heroicon-s-presentation-chart-line')
                    ->group('Eksterne Linker')
                    ->sort(7),
                NavigationItem::make('Telescope')
                    ->url('/telescope', shouldOpenInNewTab: true)
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->activeIcon('heroicon-s-presentation-chart-line')
                    ->group('Eksterne Linker')
                    ->sort(8),
            ])
            ->navigationGroups([
                'Tider',
                'Diverse',
                'Authentication',
            ])
            ->userMenuItems([
                MenuItem::make()
                    ->label('Innstillinger')
                    ->url(fn() => route('filament.admin.pages.innstillinger'))
                    ->icon('heroicon-o-cog-6-tooth'),
            ])
            ->discoverResources(in: app_path('Filament/Admin/Resources'), for: 'App\\Filament\\Admin\\Resources')
            ->discoverPages(in: app_path('Filament/Admin/Pages'), for: 'App\\Filament\\Admin\\Pages')
            ->pages([
                //Pages\Dashboard::class,
            ])
            ->discoverWidgets(in : app_path('Filament/Admin/Widgets'),
                              for: 'App\\Filament\\Admin\\Widgets')
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
            ->plugin(new FilamentEmail())
            ->plugin(FilamentSpatieLaravelBackupPlugin::make()
                ->usingPolingInterval('60s'))
            ->plugin(
                FilamentFullCalendarPlugin::make()
                    ->selectable()
                    ->editable()
                    ->plugins(['dayGrid', 'timeGrid', 'rrule', 'interaction', 'list'], true)
                    ->config([
                        'headerToolbar' => [
                            'left' => 'prev,next,today',
                            'center' => 'title',
                            'right' => 'dayGridMonth,timeGridWeek,timeGridDay,listWeek',
                        ],
                        'buttonText' => [
                            'prev' => '<',
                            'next' => '>',
                            'today' => 'Idag',
                            'month' => 'Mnd',
                            'week' => 'U',
                            'day' => 'D',
                            'prevYear' => 'Forrige år',
                            'nextYear' => 'Neste år',
                            'listMonth' => 'Agenda',
                            'listWeek' => 'UL',
                        ],
                        'slotLabelFormat' => [
                            'hour' => 'numeric',
                            'minute' => '2-digit',
                            'omitZeroMinute' => false,
                            'meridiem' => 'short',
                        ],
                        'contentHeight' => 'auto',
                        'dayMaxEvents' => true,
                        'weekNumbers' => true,
                        'weekNumberCalculation' => 'ISO',
                        'weekNumberFormat' => ['week' => 'numeric'],
                        'nowIndicator' => true,
                        'droppable' => true,
                        'displayEventEnd' => true,
                        'slotDuration' => '00:15:00',
                        'slotMinTime' => '08:00:00',
                        'slotMaxTime' => '23:00:00',
                        'navLinks' => 'true'

                    ])
            )
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
            ->topNavigation()
            ->databaseNotifications();
            //->spa();
    }
}
