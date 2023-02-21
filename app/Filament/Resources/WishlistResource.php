<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Wishlist;
use Filament\Resources\Form;
use Filament\Resources\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\WishlistResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\WishlistResource\RelationManagers;

class WishlistResource extends Resource
{
    protected static ?string $model = Wishlist::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    protected function getTableReorderColumn(): ?string
    {
        return 'prioritet';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('hva'),
                TextInput::make('koster'),
                TextInput::make('url'),
                TextInput::make('antall')->type('number'),
                Select::make('status')
                    ->options([
                        'venter' => 'Venter',
                        'begynt å spare' => 'Begynt å spare',
                        'kjøpt' => 'Kjøpt',
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('prioritet')->sortable(),
                TextColumn::make('hva')->sortable(),
                TextColumn::make('url')->formatStateUsing(fn () => 'Se her')
                    ->url(fn ($record) => $record->url, true),
                TextColumn::make('koster')->money('nok', true)->sortable(),
                TextColumn::make('antall'),
                TextColumn::make('status'),
                TextColumn::make('totalt')->money('nok', true)->getStateUsing(function (Model $record) {

                    return $record->koster * $record->antall;
                }),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])->reorderable('prioritet')->defaultSort('prioritet');;
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
            'index' => Pages\ListWishlists::route('/'),
            'create' => Pages\CreateWishlist::route('/create'),
            'edit' => Pages\EditWishlist::route('/{record}/edit'),
        ];
    }
}
