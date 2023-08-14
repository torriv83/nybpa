<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WishlistResource\Pages;
use App\Filament\Resources\WishlistResource\RelationManagers;
use App\Models\Wishlist;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class WishlistResource extends Resource
{
    protected static ?string $model = Wishlist::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationGroup = 'Diverse';

    protected static ?string $recordTitleAttribute = 'hva';

    protected static ?string $modelLabel = 'Ønskeliste';

    protected static ?string $pluralModelLabel = 'Ønskelister';

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
                        'Venter'         => 'Venter',
                        'Begynt å spare' => 'Begynt å spare',
                        'Kjøpt'          => 'Kjøpt',
                    ]),
                Placeholder::make('totalt')->content(function ($record, $set): string {

                    $totalt = $record?->find($record['id'])->wishlistitems->sum(function ($item): int {
                        return $item->koster * $item->antall;
                    });

                    $totalt > 0 ? $set('koster', $totalt) : $set('koster', $record->koster);

                    return $totalt > 0 ? $totalt : 0;

                })->label('Totalt fra Liste'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('prioritet')->sortable(),
                TextColumn::make('hva')->sortable(),
                TextColumn::make('url')->formatStateUsing(fn(): string => 'Se her')
                    ->url(fn($record) => $record->url, true),
                TextColumn::make('koster')->money('nok', true)->sortable()->summarize(Sum::make()->money('nok', true)),
                TextColumn::make('antall'),
                SelectColumn::make('status')->options([
                    'Begynt å spare' => 'Begynt å spare',
                    'Kjøpt'          => 'Kjøpt',
                    'Venter'         => 'Venter',
                ])->selectablePlaceholder(false),
                TextColumn::make('totalt')->money('nok', true)->getStateUsing(function (Model $record) {
                    return $record->koster * $record->antall;
                }),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])->reorderable('prioritet')->defaultSort('prioritet');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\TextEntry::make('hva'),
                Infolists\Components\TextEntry::make('koster')->money('nok', true),
                Infolists\Components\TextEntry::make('url')->formatStateUsing(fn(): string => 'Se her')->url(fn($record): string => $record->url,
                    true),
                Infolists\Components\TextEntry::make('antall'),
                Infolists\Components\TextEntry::make('status'),
                /*Infolists\Components\TextEntry::make('totalt')->money('nok', true)->getStateUsing(function (Model $record) {
                    return $record->koster * $record->antall;
                }),*/
                Infolists\Components\RepeatableEntry::make('WishlistItems')->label('Deler i ønskelisten')
                    ->schema([
                        Infolists\Components\TextEntry::make('hva')->url(fn($record): string => $record->url,
                            true)->columnSpanFull(),
                        Infolists\Components\TextEntry::make('antall'),
                        Infolists\Components\TextEntry::make('koster')->money('nok', true),
                    ])->columnSpanFull()->columns(2)->grid(2)
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\WishlistItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListWishlists::route('/'),
            'create' => Pages\CreateWishlist::route('/create'),
            'edit'   => Pages\EditWishlist::route('/{record}/edit'),
            //            'view'   => Pages\EditWishlist::route('/{record}'),
        ];
    }
}
