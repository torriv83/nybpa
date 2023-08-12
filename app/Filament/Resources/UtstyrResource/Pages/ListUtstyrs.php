<?php

namespace App\Filament\Resources\UtstyrResource\Pages;

use App\Filament\Resources\UtstyrResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUtstyrs extends ListRecords
{
    protected static string $resource = UtstyrResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
