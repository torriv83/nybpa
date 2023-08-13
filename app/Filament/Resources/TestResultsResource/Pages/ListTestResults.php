<?php

namespace App\Filament\Resources\TestResultsResource\Pages;

use App\Filament\Resources\TestResultsResource;
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
            TestResultsResource\Widgets\VektChart::class,
            TestResultsResource\Widgets\StyrkeChart::class,
            TestResultsResource\Widgets\RheitChart::class,
        ];
    }
}
