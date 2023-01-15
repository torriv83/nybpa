<?php

namespace App\Filament\Resources\KategoriResource\Pages;

use App\Filament\Resources\KategoriResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewKategori extends ViewRecord
{
    protected static string $resource = KategoriResource::class;

    protected function getActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
