<?php

namespace App\Filament\Landslag\Widgets;

use App\Models\TrainingProgram;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Model;

class TreningsProgrammerTable extends BaseWidget
{
    protected static ?string $pollingInterval = null;

    protected static ?string $heading = 'Treningsprogrammer';

    protected static ?int $sort = 5;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                TrainingProgram::query()->with('WorkoutExercises')
            )
            ->columns([
                TextColumn::make('program_name')
                    ->label('Program Navn')
                    ->description(fn (TrainingProgram $record): string => $record->description ?? '')
                    ->sortable(),
                TextColumn::make('antall')
                    ->getStateUsing(fn (TrainingProgram $record) => $record->WorkoutExercises->count())
                    ->sortable(),
                Textcolumn::make('created_at')
                    ->sortable()
                    ->label('Opprettet')
                    ->dateTime('d.m.Y H:i'),
            ])
            ->recordUrl(
                fn (Model $record): string => route('filament.landslag.resources.training-programs.view', ['record' => $record]),
            )
            ->emptyStateHeading('Ingen programmer')
            ->emptyStateDescription('');
    }
}
