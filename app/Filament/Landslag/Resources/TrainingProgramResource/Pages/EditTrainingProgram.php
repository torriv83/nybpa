<?php

namespace App\Filament\Landslag\Resources\TrainingProgramResource\Pages;

use App\Filament\Landslag\Resources\TrainingProgramResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTrainingProgram extends EditRecord
{
    protected static string $resource = TrainingProgramResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
