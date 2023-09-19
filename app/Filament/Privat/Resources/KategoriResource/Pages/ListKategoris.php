<?php

namespace App\Filament\Privat\Resources\KategoriResource\Pages;

use App\Filament\Privat\Resources\KategoriResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListKategoris extends ListRecords
{
    protected static string $resource = KategoriResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
