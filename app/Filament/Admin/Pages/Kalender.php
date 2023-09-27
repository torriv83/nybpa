<?php

namespace App\Filament\Admin\Pages;

use App\Filament\Admin\Resources\TimesheetResource\Widgets\CalendarWidget;
use Filament\Pages\Page;

class Kalender extends Page
{
    protected static ?string $navigationIcon = 'icon-calendar';

    protected static ?string $title = 'Kalender';

    protected static ?string $navigationGroup = 'Tider';

    protected static ?string $navigationLabel = 'Kalender';

    protected static string $view = 'filament.pages.kalender';

    protected function getFooterWidgets(): array
    {
        return [
            CalendarWidget::class
        ];
    }
}
