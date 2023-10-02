<?php

namespace App\Filament\Landslag\Resources;

use App\Filament\Landslag\Resources\TrainingProgramResource\Pages;
use App\Filament\Landslag\Resources\TrainingProgramResource\RelationManagers;
use App\Models\TrainingProgram;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TrainingProgramResource extends Resource
{
    protected static ?string $model           = TrainingProgram::class;
    protected static ?string $navigationGroup = 'Treningsprogram';
    protected static ?string $label           = 'Treningsprogram';
    protected static ?string $pluralLabel     = 'Treningsprogrammer';
    protected static ?string $navigationIcon  = 'heroicon-o-calendar-days';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('program_name')
                    ->label('Navn')
                    ->required(),
                Forms\Components\TextInput::make('description')
                    ->label('Beskrivelse'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('program_name')
                    ->label('Program Navn')
                    ->description(fn(TrainingProgram $record): string => $record->description ?? ''),
                Tables\Columns\TextColumn::make('workout_exercises_count')
                    ->counts('WorkoutExercises')
                    ->label('Antall Øvelser'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Opprettet')
                    ->date('d.m.Y H:i'),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Oppdatert')
                    ->date('d.m.Y H:i'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make()
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\WorkoutExercisesRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListTrainingPrograms::route('/'),
            'create' => Pages\CreateTrainingProgram::route('/create'),
            'edit'   => Pages\EditTrainingProgram::route('/{record}/edit'),
            'view'   => Pages\ViewTrainingProgram::route('/{record}'),
        ];
    }
}
