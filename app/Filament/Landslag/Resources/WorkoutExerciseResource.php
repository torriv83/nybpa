<?php

namespace App\Filament\Landslag\Resources;

use App\Filament\Landslag\Resources\WorkoutExerciseResource\Pages;
use App\Models\WorkoutExercise;
use Exception;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class WorkoutExerciseResource extends Resource
{
    protected static ?string $model = WorkoutExercise::class;

    protected static ?string $navigationGroup = 'Treningsprogram';

    protected static ?string $label = 'Øvelse';

    protected static ?string $pluralLabel = 'Øvelser';

    protected static ?string $navigationIcon = 'icon-exercise';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('exercise_name')
                    ->label('Navn')
                    ->required(),
            ]);
    }

    /**
     * @throws Exception
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('exercise_name')
                    ->label('Navn')
                    ->sortable(),
                Tables\Columns\TextColumn::make('IAntallProgrammer')
                    ->getStateUsing(function (WorkoutExercise $record) {
                        return $record->TrainingPrograms->count();
                    }),
                Tables\Columns\TextColumn::make('created_at')->dateTime('d.m.y H:i')->label('Lagt til'),
            ])->reorderable()
            ->filters([
                TernaryFilter::make('arkivert')
                    ->placeholder('Uten arkiverte øvelser')
                    ->trueLabel('Med arkiverte øvelser')
                    ->falseLabel('Bare arkiverte øvelser')
                    ->queries(
                        true : fn (Builder $query) => $query->withTrashed(),
                        false: fn (Builder $query) => $query->onlyTrashed(),
                        blank: fn (Builder $query) => $query->withoutTrashed(),
                    ),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label('Arkiver'),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
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
            'index' => Pages\ListWorkoutExercises::route('/'),
            'create' => Pages\CreateWorkoutExercise::route('/create'),
            'edit' => Pages\EditWorkoutExercise::route('/{record}/edit'),
        ];
    }
}
