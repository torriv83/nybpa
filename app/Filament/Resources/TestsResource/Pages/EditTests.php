<?php

namespace App\Filament\Resources\TestsResource\Pages;

use App\Filament\Resources\TestsResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTests extends EditRecord
{
    protected static string $resource = TestsResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
