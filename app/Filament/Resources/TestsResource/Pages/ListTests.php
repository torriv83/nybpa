<?php

namespace App\Filament\Resources\TestsResource\Pages;

use App\Filament\Resources\TestsResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTests extends ListRecords
{
    protected static string $resource = TestsResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}