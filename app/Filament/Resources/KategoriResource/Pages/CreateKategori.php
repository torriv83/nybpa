<?php

namespace App\Filament\Resources\KategoriResource\Pages;

use App\Filament\Resources\KategoriResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Cache;

class CreateKategori extends CreateRecord
{
    protected static string $resource = KategoriResource::class;

    protected function getRedirectUrl(): string
    {

        return $this->getResource()::getUrl('index');
    }

    protected function afterCreate(): void
    {
        Cache::tags(['medisinsk'])->flush();
    }
}
