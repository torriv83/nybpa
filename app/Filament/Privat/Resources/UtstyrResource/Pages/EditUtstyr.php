<?php

namespace App\Filament\Privat\Resources\UtstyrResource\Pages;

use App\Filament\Privat\Resources\UtstyrResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUtstyr extends EditRecord
{
    protected static string $resource = UtstyrResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
