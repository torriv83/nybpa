<?php

namespace App\Filament\Privat\Resources\KategoriResource\Pages;

use App\Filament\Privat\Resources\KategoriResource;
use Exception;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Cache;

class EditKategori extends EditRecord
{
    protected static string $resource = KategoriResource::class;

    /**
     * @throws Exception
     */
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

    protected function getRedirectUrl(): string
    {

        return $this->getResource()::getUrl('index');
    }

    protected function afterSave(): void
    {
        Cache::tags(['medisinsk'])->flush();
    }
}
