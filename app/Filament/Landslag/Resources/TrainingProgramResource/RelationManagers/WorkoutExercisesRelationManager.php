<?php

namespace App\Filament\Landslag\Resources\TrainingProgramResource\RelationManagers;

use App\Models\WorkoutExercise;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\AttachAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class WorkoutExercisesRelationManager extends RelationManager
{
    protected static string $relationship = 'WorkoutExercises';

    public static function getTitle(Model $ownerRecord,string $pageClass) : string{
        return 'Øvelser';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('description')
                    ->label('Beskrivelse'),
                Forms\Components\TextInput::make('repetitions')
                    ->label('Reps')
                    ->required(),
                Forms\Components\TextInput::make('sets')
                    ->label('Set')
                    ->required(),
                Forms\Components\TimePicker::make('rest')->label('Pause')
                    ->placeholder('00:00')
                    ->displayFormat('i:s')
                    ->format('H:i:s')
                    ->native(false)
                    ->secondsStep(10)
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('exercise_name')
                    ->description(fn (WorkoutExercise $record): string => $record->description)
                    ->label('Øvelse'),
                Tables\Columns\TextColumn::make('repetitions')->label('Reps'),
                Tables\Columns\TextColumn::make('sets')->label('Set'),
                Tables\Columns\TextColumn::make('rest')->label('Pause')->time('i:s'),
            ])
            ->reorderable('order')
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->label('Legg til øvelse i program')
                    ->recordSelect(fn () => Select::make('recordId')->label('Øvelse')
                        ->options(WorkoutExercise::all()->pluck('exercise_name', 'id'))
                        ->searchable()
                        ->preload()
                        ->createOptionForm([
                            Forms\Components\TextInput::make('exercise_name')->label('Øvelse'),
                        ])
                        ->createOptionUsing(function ($data){
                            $exercise = WorkoutExercise::create($data);
                            return $exercise->getKey();
                        })
                    )
                    ->form(fn (AttachAction $action): array => [
                        $action->getRecordSelect(),
                        Forms\Components\TextInput::make('description'),
                        Forms\Components\TextInput::make('repetitions')->required(),
                        Forms\Components\TextInput::make('sets')->required(),
                        Forms\Components\TimePicker::make('rest')
                            ->native(false)
                            ->secondsStep(10)
                            ->displayFormat('i:s')
                            ->format('H:i:s')
                            ->required(),
                        Forms\Components\Hidden::make('id')
                    ])
            ])
            ->actions([
                Tables\Actions\DetachAction::make()->label('Fjern fra program'),
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
                //Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
