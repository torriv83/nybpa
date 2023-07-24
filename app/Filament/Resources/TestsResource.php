<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TestsResource\Pages;
use App\Filament\Resources\TestsResource\RelationManagers;
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

    protected static ?string $navigationIcon   = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup  = 'Landslag';
    protected static ?string $modelLabel       = 'Test';
    protected static ?string $pluralModelLabel = 'Tester';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('navn')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Repeater::make('ovelser')
                    ->label('Øvelser')
                    ->schema([
                        Forms\Components\TextInput::make('navn')->required(),
                        Forms\Components\Select::make('type')
                            ->options([
                                'kg'   => 'Kg',
                                'tid'  => 'Tid',
                                'watt' => 'Watt',
                            ])
                            ->required(),
                    ])
                    ->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('navn'),
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
            'index'  => Pages\ListTests::route('/'),
            'create' => Pages\CreateTests::route('/create'),
            'edit'   => Pages\EditTests::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
