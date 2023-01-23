<?php

namespace App\Filament\Resources\EconomyResource\Pages;

use App\Filament\Resources\EconomyResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageEconomies extends ManageRecords
{

    protected static string $resource = EconomyResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            EconomyResource\Widgets\StatsOverview::class,
        ];
    }
}
