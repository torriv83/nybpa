<?php

namespace App\Filament\Landslag\Resources\WorkoutExerciseResource\Pages;

use App\Filament\Landslag\Resources\WorkoutExerciseResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListWorkoutExercises extends ListRecords
{
    protected static string $resource = WorkoutExerciseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
