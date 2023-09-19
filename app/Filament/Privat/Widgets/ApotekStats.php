<?php

namespace App\Filament\Privat\Widgets;

use App\Filament\Privat\Resources\UtstyrResource;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ApotekStats extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Antall utstyr på lista', UtstyrResource::getEloquentQuery()->where('deleted_at', null)->count())
        ];
    }
}
