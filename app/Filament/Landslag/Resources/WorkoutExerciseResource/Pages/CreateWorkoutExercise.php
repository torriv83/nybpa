<?php

namespace App\Filament\Landslag\Resources\WorkoutExerciseResource\Pages;

use App\Filament\Landslag\Resources\WorkoutExerciseResource;
use Filament\Resources\Pages\CreateRecord;

class CreateWorkoutExercise extends CreateRecord
{
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    protected static string $resource = WorkoutExerciseResource::class;
}
