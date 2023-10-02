<?php

namespace App\Filament\Landslag\Resources\DayResource\Pages;

use App\Filament\Landslag\Resources\DayResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDay extends CreateRecord
{
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    protected static string $resource = DayResource::class;
}
