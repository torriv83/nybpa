<?php

namespace App\Filament\Landslag\Resources;

use App\Filament\Landslag\Resources\WorkoutExerciseResource\Pages;
use App\Filament\Landslag\Resources\WorkoutExerciseResource\RelationManagers;
use App\Models\WorkoutExercise;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class WorkoutExerciseResource extends Resource
{
    protected static ?string $model           = WorkoutExercise::class;
    protected static ?string $navigationGroup = 'Treningsprogram';
    protected static ?string $label           = 'Øvelse';
    protected static ?string $pluralLabel     = 'Øvelser';
    protected static ?string $navigationIcon  = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('exercise_name')
                    ->label('Navn')
                    ->required()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('exercise_name')->label('Navn'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListWorkoutExercises::route('/'),
            'create' => Pages\CreateWorkoutExercise::route('/create'),
            'edit'   => Pages\EditWorkoutExercise::route('/{record}/edit'),
        ];
    }
}
