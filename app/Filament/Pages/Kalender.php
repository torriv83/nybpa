<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Filament\Widgets\CalendarWidget;

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
            CalendarWidget::class
        ];
    }
}
