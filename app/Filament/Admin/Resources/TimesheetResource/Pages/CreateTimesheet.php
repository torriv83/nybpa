<?php

namespace App\Filament\Admin\Resources\TimesheetResource\Pages;

use App\Filament\Admin\Resources\TimesheetResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Cache;

class CreateTimesheet extends CreateRecord
{
    protected static string $resource = TimesheetResource::class;

    protected function getRedirectUrl(): string
    {

        return $this->getResource()::getUrl('index');
    }

    protected function afterCreate(): void
    {
        Cache::tags(['timesheet'])->flush();
    }
}