<?php

namespace App\Filament\Resources\TestResultsResource\Pages;

use App\Filament\Resources\TestResultsResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTestResults extends EditRecord
{
    protected static string $resource = TestResultsResource::class;

    /**
     * @throws \Exception
     */
    protected function getHeaderActions(): array
    {

        return [
            Actions\DeleteAction::make()->label('Slett'),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
