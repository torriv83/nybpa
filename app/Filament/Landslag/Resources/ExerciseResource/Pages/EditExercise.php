<?php

namespace App\Filament\Landslag\Resources\ExerciseResource\Pages;

use App\Filament\Landslag\Resources\ExerciseResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditExercise extends EditRecord
{
    protected static string $resource = ExerciseResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
