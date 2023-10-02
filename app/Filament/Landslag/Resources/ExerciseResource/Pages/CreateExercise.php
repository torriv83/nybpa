<?php

namespace App\Filament\Landslag\Resources\ExerciseResource\Pages;

use App\Filament\Landslag\Resources\ExerciseResource;
use Filament\Resources\Pages\CreateRecord;

class CreateExercise extends CreateRecord
{
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    protected static string $resource = ExerciseResource::class;
}
