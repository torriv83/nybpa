<?php

namespace App\Filament\Landslag\Resources\WeekplanResource\Pages;

use App\Filament\Landslag\Resources\WeekplanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListWeekplans extends ListRecords
{
    protected static string $resource = WeekplanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
