<?php

namespace App\Filament\Resources\TimesheetResource\Pages;

use App\Filament\Resources\TimesheetResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTimesheets extends ListRecords
{
    protected static string $resource = TimesheetResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Ny oppføring'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            TimesheetResource\Widgets\HoursUsedEachMonth::class,
        ];
    }
}
