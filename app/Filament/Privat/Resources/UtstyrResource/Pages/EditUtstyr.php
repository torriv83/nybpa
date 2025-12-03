<?php

namespace App\Filament\Privat\Resources\UtstyrResource\Pages;

use App\Filament\Privat\Resources\UtstyrResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Cache;

class EditUtstyr extends EditRecord
{
    protected static string $resource = UtstyrResource::class;

    protected function getRedirectUrl(): string
    {

        return $this->getResource()::getUrl('index');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make()
                ->after(fn () => Cache::tags(['medisinsk'])->flush()),
            Actions\ForceDeleteAction::make()
                ->after(fn () => Cache::tags(['medisinsk'])->flush()),
            Actions\RestoreAction::make()
                ->after(fn () => Cache::tags(['medisinsk'])->flush()),
        ];
    }

    protected function afterSave(): void
    {
        Cache::tags(['medisinsk'])->flush();
    }
}
