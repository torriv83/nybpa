<?php

namespace App\Filament\Landslag\Resources;

use App\Filament\Landslag\Resources\TestsResource\Pages\CreateTests;
use App\Filament\Landslag\Resources\TestsResource\Pages\EditTests;
use App\Filament\Landslag\Resources\TestsResource\Pages\ListTests;
use App\Models\Tests;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TestsResource extends Resource
{
    protected static ?string $model = Tests::class;

    protected static ?string $navigationIcon = 'icon-type-test';

    protected static ?string $navigationGroup = 'Tester';

    protected static ?string $modelLabel = 'Test';

    protected static ?string $pluralModelLabel = 'Type Tester';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('navn')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Section::make([
                    Forms\Components\Repeater::make('ovelser')
                        ->label('Øvelser')
                        ->schema([
                            Forms\Components\TextInput::make('navn')->required(),
                            Forms\Components\Select::make('type')
                                ->options([
                                    'kg' => 'Kg',
                                    'tid' => 'Tid',
                                    'watt' => 'Watt',
                                    'rep' => 'Repetisjoner',
                                ])
                                ->required(),
                        ])->columns(),
                ]),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('navn'),
                Tables\Columns\ViewColumn::make('ovelser')
                    ->label('Øvelse : Type')
                    ->view('filament.resources.tests-resource.tests-type-column'),
                Tables\Columns\TextColumn::make('updated_at')
                    ->since()
                    ->label('Sist oppdatert'),
                Tables\Columns\TextColumn::make('created_at')
                    ->since()
                    ->label('Opprettet'),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
                Tables\Actions\ForceDeleteBulkAction::make(),
                Tables\Actions\RestoreBulkAction::make(),
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
            'index' => ListTests::route('/'),
            'create' => CreateTests::route('/create'),
            'edit' => EditTests::route('/{record}/edit'),
        ];
    }

    /**
     * @return Builder<Tests>
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
