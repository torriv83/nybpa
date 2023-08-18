<?php

namespace App\Filament\Assistent\Resources\TimesheetResource\Pages;

use App\Filament\Assistent\Resources\TimesheetResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTimesheet extends CreateRecord
{
    protected static string $resource = TimesheetResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
