<?php

namespace App\Filament\Landslag\Resources\TrainingProgramResource\Pages;

use App\Filament\Landslag\Resources\TrainingProgramResource;
use App\Models\TrainingProgram;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Resources\Pages\Page;

class ViewTrainingProgram extends Page
{
    protected static string $resource = TrainingProgramResource::class;

    protected static string $view = 'filament.resources.training-program-resource.pages.view-treningsprogram';

    protected function getViewData(): array
    {
        $data = $this->record->WorkoutExercises()->get();

        return compact('data');
    }

    public function getTitle(): string
    {
        // Retrieve the dynamic title from a database, configuration, or any other source
        return $this->record['program_name'];
    }

    public function getSubheading(): string
    {
        $dynamicSubheading = $this->record['updated_at'];
        $formattedDate = '';

        if ($dynamicSubheading) {
            $formattedDate = Carbon::parse($dynamicSubheading)->diffForHumans();
        }

        return 'Sist oppdatert: '.$formattedDate;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('edit')->url(route('filament.landslag.resources.training-programs.edit', ['record' => $this->record->id]))->label('Endre'),
        ];
    }

    public function mount(int|string $record): void
    {
        $this->record = TrainingProgram::find($record);
    }

    protected function getHeaderWidgets(): array
    {
        return [
            // ...
        ];
    }
}
