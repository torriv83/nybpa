<?php

namespace App\Filament\Landslag\Resources\WeekplanResource\Pages;

use App\Filament\Landslag\Resources\WeekplanResource;
use Filament\Resources\Pages\CreateRecord;

class CreateWeekplan extends CreateRecord
{
/*    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }*/
    protected static string $resource = WeekplanResource::class;
}
