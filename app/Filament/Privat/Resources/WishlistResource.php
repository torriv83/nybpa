<?php

namespace App\Filament\Privat\Resources;

use App\Filament\Privat\Resources\WishlistResource\Pages\CreateWishlist;
use App\Filament\Privat\Resources\WishlistResource\Pages\EditWishlist;
use App\Filament\Privat\Resources\WishlistResource\Pages\ListWishlists;
use App\Filament\Privat\Resources\WishlistResource\RelationManagers\WishlistItemsRelationManager;
use App\Models\Wishlist;
use Exception;
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
use Filament\Tables\Columns\Summarizers\Summarizer;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Query\Builder;

class WishlistResource extends Resource
{
    protected static ?string $model = Wishlist::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $recordTitleAttribute = 'hva';

    protected static ?string $modelLabel = 'Ønskeliste';

    protected static ?string $pluralModelLabel = 'Ønskelister';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('hva')
                    ->required()
                    ->autofocus(),
                TextInput::make('koster')
                    ->default(0)
                    ->numeric()
                    ->required(),
                TextInput::make('url')
                    ->type('url')
                    ->required()
                    ->default('https://example.com'),
                TextInput::make('antall')
                    ->numeric()
                    ->required(),
                Select::make('status')
                    ->options(Wishlist::STATUS_OPTIONS)
                    ->placeholder('Velg status'),
                Placeholder::make('totalt')->content(function ($record, $set) {
                    $totalt = $record?->find($record['id'])->wishlistitems->sum(function ($item) {
                        return $item->koster * $item->antall;
                    });

                    $totalt > 0 ? $set('koster', $totalt) : $set('koster', $record?->koster);

                    return $totalt > 0 ? $totalt : 0;
                })->label('Totalt fra Liste'),
            ]);
    }

    /**
     * @throws Exception
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('prioritet')->sortable(),
                TextColumn::make('hva')->sortable(),
                TextColumn::make('url')->formatStateUsing(fn () => 'Se her')
                    ->url(fn ($record) => $record->url, true),
                TextColumn::make('koster')
                    ->money('nok', 1)
                    ->sortable()
                    ->summarize(Sum::make()
                        ->money('NOK', 1)),
                TextColumn::make('antall'),
                SelectColumn::make('status')->options(Wishlist::STATUS_OPTIONS)
                    ->selectablePlaceholder(false)
                    ->summarize(
                        Summarizer::make()
                            ->money('nok', 1)
                            ->label('left')
                            ->label('Gjenstår')
                            ->using(function (Builder $query): float {
                                $total = $query->sum('koster');
                                $doneSaving = $query->where('status', '=', 'Spart')
                                    ->orWhere('status', '=', 'Kjøpt')
                                    ->sum('koster');

                                return $total - $doneSaving;
                            })
                    ),
                TextColumn::make('totalt')
                    ->money('nok', 1)
                    ->getStateUsing(function (Wishlist $record) {
                        return $record->koster * $record->antall;
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(Wishlist::STATUS_OPTIONS)
                    ->placeholder('Velg status'),
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
                Infolists\Components\Section::make(fn ($record) => $record->hva)
                    ->schema([
                        Infolists\Components\TextEntry::make('koster')->money('nok', 1),
                        Infolists\Components\TextEntry::make('url')->formatStateUsing(fn () => 'Se her')->url(fn ($record): string => $record->url,
                            true),
                        Infolists\Components\TextEntry::make('antall'),
                        Infolists\Components\TextEntry::make('status'),

                    ])->columns(4),

                Infolists\Components\RepeatableEntry::make('WishlistItems')->label('Deler i ønskelisten')
                    ->schema([
                        Infolists\Components\TextEntry::make('hva')
                            ->hiddenLabel()
                            ->url(fn ($record): string => $record->url, true)
                            ->columnSpanFull(),
                        Infolists\Components\TextEntry::make('antall'),
                        Infolists\Components\TextEntry::make('koster')->money('nok', 1),
                    ])->columnSpanFull()->columns()->grid(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            WishlistItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListWishlists::route('/'),
            'create' => CreateWishlist::route('/create'),
            'edit' => EditWishlist::route('/{record}/edit'),
            //            'view'   => Pages\EditWishlist::route('/{record}'),
        ];
    }
}
