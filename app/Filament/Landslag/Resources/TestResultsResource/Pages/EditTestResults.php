<?php

namespace App\Filament\Landslag\Resources\TestResultsResource\Pages;

use App\Filament\Landslag\Resources\TestResultsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Cache;

class EditTestResults extends EditRecord
{
    protected static string $resource = TestResultsResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    /**
     * @throws \Exception
     */
    protected function getHeaderActions(): array
    {

        return [
            Actions\DeleteAction::make()->label('Slett')->after(function(){Cache::tags(['testresult'])->flush();}),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        Cache::tags(['testresult'])->flush();
    }
}
