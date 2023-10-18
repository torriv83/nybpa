<?php

namespace App\Filament\Admin\Resources\TimesheetResource\Pages;

use App\Filament\Admin\Resources\TimesheetResource;
use App\Traits\DateAndTimeHelper;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Cache;

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
            Actions\DeleteAction::make()->label('Slett'),
            Actions\ForceDeleteAction::make()->label('Tving sletting'),
            Actions\RestoreAction::make()->label('Angre sletting'),
        ];
    }

    protected function afterSave(): void
    {
        Cache::tags(['timesheet'])->flush();
    }
}
