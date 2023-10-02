<?php

namespace App\Filament\Landslag\Resources\TestsResource\Pages;

use App\Filament\Landslag\Resources\TestsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTests extends EditRecord
{
    protected static string $resource = TestsResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
