<?php

namespace App\Filament\Privat\Resources\ResepterResource\Pages;

use App\Filament\Privat\Resources\ResepterResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Cache;

class EditResepter extends EditRecord
{
    protected static string $resource = ResepterResource::class;

    protected function getRedirectUrl(): string
    {

        return $this->getResource()::getUrl('index');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->after(fn () => Cache::tags(['medisinsk'])->flush()),
        ];
    }

    protected function afterSave(): void
    {
        Cache::tags(['medisinsk'])->flush();
    }
}
