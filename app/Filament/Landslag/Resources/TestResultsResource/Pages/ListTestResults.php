<?php

namespace App\Filament\Landslag\Resources\TestResultsResource\Pages;

use App\Filament\Landslag\Resources\TestResultsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTestResults extends ListRecords
{
    protected static string $resource = TestResultsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            // ... 
        ];
    }
}
