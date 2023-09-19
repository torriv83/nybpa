<?php

namespace App\Filament\Privat\Resources\KategoriResource\Pages;

use App\Filament\Privat\Resources\KategoriResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Cache;

class EditKategori extends EditRecord
{
    protected static string $resource = KategoriResource::class;

    /**
     * @throws \Exception
     */
    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        Cache::tags(['medisinsk'])->flush();
    }
}
