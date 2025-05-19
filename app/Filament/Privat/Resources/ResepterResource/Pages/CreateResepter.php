<?php

namespace App\Filament\Privat\Resources\ResepterResource\Pages;

use App\Filament\Privat\Resources\ResepterResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Cache;

class CreateResepter extends CreateRecord
{
    protected static string $resource = ResepterResource::class;

    protected function getRedirectUrl(): string
    {

        return $this->getResource()::getUrl('index');
    }

    protected function afterCreate(): void
    {
        Cache::tags(['medisinsk'])->flush();
    }

    protected function afterSave(): void
    {
        Cache::tags(['medisinsk'])->flush();
    }
}
