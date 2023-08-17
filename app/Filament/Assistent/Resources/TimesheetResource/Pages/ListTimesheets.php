<?php

namespace App\Filament\Assistent\Resources\TimesheetResource\Pages;

use App\Filament\Assistent\Resources\TimesheetResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTimesheets extends ListRecords
{
    protected static string $resource = TimesheetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Legg til tider du ikke kan jobbe'),
        ];
    }
}
