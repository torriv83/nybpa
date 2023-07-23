<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BasePage;

class Dashboard extends BasePage
{

    protected function getColumns(): int|array
    {
        return 6;

    }

    protected function getHeading(): string
    {
        return 'BPA Dashboard';
    }
}
