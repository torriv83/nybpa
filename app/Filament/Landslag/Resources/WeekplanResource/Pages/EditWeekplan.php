<?php

namespace App\Filament\Landslag\Resources\WeekplanResource\Pages;

use App\Filament\Landslag\Resources\WeekplanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditWeekplan extends EditRecord
{
    protected static string $resource = WeekplanResource::class;

    /*    protected function getRedirectUrl(): string
        {
            return $this->getResource()::getUrl('index');
        }*/
    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
