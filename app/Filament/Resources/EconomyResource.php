<?php

/** @noinspection PhpFullyQualifiedNameUsageInspection */

/** @noinspection PhpUnused */

namespace App\Filament\Resources;

use App\Filament\Resources\EconomyResource\Pages;
use app\Filament\Resources\EconomyResource\Widgets\StatsOverview;
use App\Filament\Resources\EconomyResource\Widgets\YnabOverview;
use App\Models\Economy;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class EconomyResource extends Resource
{
    protected static ?string $model = Economy::class;

    protected static ?string $navigationGroup = 'Diverse';

    protected static ?string $modelLabel = 'Økonomi';

    protected static ?string $pluralModelLabel = 'Økonomi';

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    public static function form(Form $form): Form
    {

        return $form
            ->schema([
                Forms\Components\TextInput::make('before_tax')
                    ->required(),
                Forms\Components\TextInput::make('after_tax')
                    ->required(),
                Forms\Components\TextInput::make('tax_table')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('grunnstonad'),
            ]);
    }

    /**
     * @throws \Exception
     */
    public static function table(Table $table): Table
    {

        return $table
            ->columns([
                Tables\Columns\TextColumn::make('before_tax')->label('Før skatt')->money('nok', true),
                Tables\Columns\TextColumn::make('after_tax')->label('Etter skatt')->money('nok', true),
                Tables\Columns\TextColumn::make('tax_table')->label('Skattetabell'),
                Tables\Columns\TextColumn::make('grunnstonad')->money('nok', true)
                    ->label('Grunnstønad'),
                Tables\Columns\TextColumn::make('updated_at')->label('Oppdatert')
                    ->dateTime('d.m.Y'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageEconomies::route('/'),
        ];
    }

    public static function getWidgets(): array
    {
        return [
            StatsOverview::class,
            YnabOverview::class,
        ];
    }
}
