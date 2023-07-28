<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\CalendarWidget;
use Filament\Pages\Page;

class Kalender extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    protected static ?string $title = 'Kalender';

    protected static ?string $navigationGroup = 'Tider';

    protected static ?string $navigationLabel = 'Kalender';

    protected static string $view = 'filament.pages.kalender';

    protected function getFooterWidgets(): array
    {
        return [
            CalendarWidget::class,
        ];
    }
}
