<?php

namespace App\Filament\Privat\Resources\WishlistResource\RelationManagers;

use App\Models\Wishlist;
use DB;
use Exception;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\Summarizers\Summarizer;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;

class WishlistItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'wishlistItems';

    protected static ?string $recordTitleAttribute = 'hva';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('hva')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('url')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('koster')
                    ->required()->numeric(),
                Forms\Components\TextInput::make('antall')
                    ->required()->numeric(),
                Forms\Components\Select::make('status')
                    ->options(Wishlist::STATUS_OPTIONS)
                    ->required(),
            ]);
    }

    /**
     * @throws Exception
     */
    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('hva'),

                Tables\Columns\TextColumn::make('url')
                    ->formatStateUsing(fn () => 'Se her')
                    ->url(fn ($record): string => $record->url, true),

                Tables\Columns\TextColumn::make('koster')
                    ->money('nok', 1)
                    ->sortable()
                    ->summarize(Sum::make()->money('nok', 1)),
                Tables\Columns\TextColumn::make('antall'),

                SelectColumn::make('status')
                    ->options(Wishlist::STATUS_OPTIONS)
                    ->sortable()
                    ->selectablePlaceholder(false)
                    ->summarize(Summarizer::make()
                        ->money('nok', 1)
                        ->label('Gjenstår')
                        ->using(function (Builder $query): string {
                            return $query->whereNotIn('status', ['Spart', 'Kjøpt'])
                                ->sum('koster');
                        })),
                Tables\Columns\TextColumn::make('totalt')
                    ->money('nok', 1)
                    ->getStateUsing(function (Model $record) {
                        return $record->koster * $record->antall;
                    })
                    ->summarize(Summarizer::make()
                        ->label('Totalt')
                        ->money('nok', 1)
                        ->using(function (Builder $query): string {
                            // Calculate the sum of the product of 'koster' and 'antall' directly in the query
                            return $query->sum(DB::raw('koster * antall'));
                        }),
                    ),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->multiple()
                    ->options(Wishlist::STATUS_OPTIONS)
                    ->placeholder('Velg status'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->after(fn (RelationManager $livewire, Model $record) => $this->dispatchItemEdited($livewire, $record)),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->after(fn (RelationManager $livewire, Model $record) => $this->dispatchItemEdited($livewire, $record)),
                Tables\Actions\DeleteAction::make()
                    ->after(fn (RelationManager $livewire, Model $record) => $this->dispatchItemEdited($livewire, $record)),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->after(function (RelationManager $livewire, Model $record) {
                            $livewire->dispatch('itemedited', $record->wishlist_id);
                        }),
                    Tables\Actions\BulkAction::make('status')
                        ->label('Endre status')
                        ->deselectRecordsAfterCompletion()
                        ->form([
                            Forms\Components\Select::make('status')
                                ->options(Wishlist::STATUS_OPTIONS)
                                ->required(),
                        ])
                        ->action(function (array $data, Collection $records) {
                            $records->each(function (Model $record) use ($data) {
                                $record->update(['status' => $data['status']]);
                            });
                        }),
                ]),
            ]);
    }

    private function dispatchItemEdited(RelationManager $livewire, Model $record): void
    {
        $livewire->dispatch('itemedited', $record->wishlist_id);
    }
}
