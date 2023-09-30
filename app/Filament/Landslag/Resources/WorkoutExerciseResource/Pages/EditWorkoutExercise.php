<?php

namespace App\Filament\Landslag\Resources\WorkoutExerciseResource\Pages;

use App\Filament\Landslag\Resources\WorkoutExerciseResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditWorkoutExercise extends EditRecord
{
    protected static string $resource = WorkoutExerciseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
