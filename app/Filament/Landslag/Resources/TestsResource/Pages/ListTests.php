<?php

namespace App\Filament\Landslag\Resources\TestsResource\Pages;

use App\Filament\Landslag\Resources\TestsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTests extends ListRecords
{
    protected static string $resource = TestsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
