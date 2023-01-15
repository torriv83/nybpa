<?php

namespace App\Filament\Resources\UtstyrResource\Pages;

use App\Filament\Resources\UtstyrResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUtstyr extends EditRecord
{
    protected static string $resource = UtstyrResource::class;

    protected function getActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
