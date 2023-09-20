<?php

namespace App\Filament\Privat\Resources\ResepterResource\Pages;

use App\Filament\Privat\Resources\ResepterResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListResepters extends ListRecords
{
    protected static string $resource = ResepterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
