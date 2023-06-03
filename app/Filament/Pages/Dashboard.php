<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BasePage;

class Dashboard extends BasePage
{

    protected function getColumns(): int|array
    {
        return [
            'xs' => 12,
            'sm' => 12,
            'md' => 12,
            'lg' => 12,
        ];
    }

    protected function getHeading(): string
    {
        return 'BPA Dashboard';
    }
}
