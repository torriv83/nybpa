<?php

namespace App\Filament\Assistent\Resources\TimesheetResource\Pages;

use App\Filament\Assistent\Resources\TimesheetResource;
use App\Traits\DateAndTimeHelper;
use Filament\Resources\Pages\EditRecord;

class EditTimesheet extends EditRecord
{
    use DateAndTimeHelper;
    protected static string $resource = TimesheetResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {

        return self::transformFormDataForFill($data);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {

        return self::transformFormDataForSave($data);
    }

    protected function getHeaderActions(): array
    {
        return [
            // Actions\DeleteAction::make(),
        ];
    }
}
