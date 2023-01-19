<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Utstyr;
use Filament\Resources\Form;
use Filament\Resources\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\ReplicateAction;
use Illuminate\Database\Eloquent\Collection;
use App\Filament\Resources\UtstyrResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\UtstyrResource\RelationManagers;

class UtstyrResource extends Resource
{
    protected static ?string $model            = Utstyr::class;
    protected static ?string $navigationGroup  = 'Medisinsk';
    protected static ?string $modelLabel       = 'Utstyr';
    protected static ?string $pluralModelLabel = 'Utstyr';
    protected static ?int $navigationSort      = 1;
    protected static ?string $navigationIcon   = 'heroicon-o-collection';

    protected static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('hva'),
                TextInput::make('navn'),
                TextInput::make('artikkelnummer'),
                TextInput::make('link'),
                Select::make('kategori')
                    ->relationship('kategori', 'kategori'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('hva')->sortable(),
                TextColumn::make('navn')->sortable(),
                TextColumn::make('kategori.kategori')->sortable(),
                TextColumn::make('artikkelnummer'),
                TextColumn::make('link')
                    ->formatStateUsing(fn () => 'Se her')
                    ->url(fn ($record) => $record->link, true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
                Tables\Filters\SelectFilter::make('kategori')
                    ->relationship('kategori', 'kategori'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
                Tables\Actions\ForceDeleteBulkAction::make(),
                Tables\Actions\RestoreBulkAction::make(),
                BulkAction::make('bestillValgteProdukter')
                    ->action(function (Collection $records, array $data): void {
                        $data;
                    })
                    ->form([
                        Forms\Components\TextInput::make('navn')
                            ->label('Navn'),
                        Forms\Components\TextInput::make('antall')
                            ->numeric(),
                    ])
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListUtstyrs::route('/'),
            'create' => Pages\CreateUtstyr::route('/create'),
            'view'   => Pages\ViewUtstyr::route('/{record}'),
            'edit'   => Pages\EditUtstyr::route('/{record}/edit'),
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
