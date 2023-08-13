<?php
/**
 * Created by ${USER}.
 * Date: 18.04.2023
 * Time: 06.48
 * Company: Rivera Consulting
 */

namespace App\Filament\Resources;

use App\Filament\Resources\ExerciseResource\Pages;
use App\Models\Exercise;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ExerciseResource extends Resource
{
    protected static ?string $model = Exercise::class;

    protected static ?string $slug = 'exercises';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Landslag';

    protected static ?string $modelLabel = 'Øvelse';

    protected static ?string $pluralModelLabel = 'Øvelser';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required(),

                Placeholder::make('created_at')
                    ->label('Created Date')
                    ->content(fn(?Exercise $record): string => $record?->created_at?->diffForHumans() ?? '-'),

                Placeholder::make('updated_at')
                    ->label('Last Modified Date')
                    ->content(fn(?Exercise $record): string => $record?->updated_at?->diffForHumans() ?? '-'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('created_at')->label('Opprettet')->since(),
                TextColumn::make('updated_at')->label('Sist oppdatert')->since()
            ])->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListExercises::route('/'),
            'create' => Pages\CreateExercise::route('/create'),
            'edit'   => Pages\EditExercise::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name'];
    }
}
