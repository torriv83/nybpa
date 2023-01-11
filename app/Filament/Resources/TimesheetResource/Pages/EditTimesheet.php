<?php

namespace App\Filament\Resources\TimesheetResource\Pages;

use App\Filament\Resources\TimesheetResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EditTimesheet extends EditRecord
{
    protected static string $resource = TimesheetResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make()->label('Slett'),
            Actions\ForceDeleteAction::make()->label('Tving sletting'),
            Actions\RestoreAction::make()->label('Angre sletting'),
        ];
    }
}
