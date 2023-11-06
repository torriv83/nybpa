<?php

namespace App\Filament\Privat\Resources\WishlistResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

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
                    ->options([
                        'Begynt å spare' => 'Begynt å spare',
                        'Spart'          => 'Spart',
                        'Kjøpt'          => 'Kjøpt',
                        'Venter'         => 'Venter',
                    ])
                    ->required()
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('hva'),
                Tables\Columns\TextColumn::make('url')->formatStateUsing(fn() => 'Se her')
                    ->url(fn($record): string => $record->url, true),
                Tables\Columns\TextColumn::make('koster')->money('nok', true)->sortable()->summarize(Sum::make()),
                Tables\Columns\TextColumn::make('antall'),
                SelectColumn::make('status')->options([
                    'Begynt å spare' => 'Begynt å spare',
                    'Spart'          => 'Spart',
                    'Kjøpt'          => 'Kjøpt',
                    'Venter'         => 'Venter',
                ])->selectablePlaceholder(false),
                Tables\Columns\TextColumn::make('totalt')->money('nok', true)->getStateUsing(function (Model $record)
                {
                    return $record->koster * $record->antall;
                }),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->after(function (RelationManager $livewire, Model $record)
                    {
                        // Runs after the form fields are saved to the database.
                        $livewire->dispatch('itemedited', $record->wishlist_id);
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->after(function (RelationManager $livewire, Model $record)
                    {
                        // Runs after the form fields are saved to the database.
                        $livewire->dispatch('itemedited', $record->wishlist_id);
                    }),
                Tables\Actions\DeleteAction::make()
                    ->after(function (RelationManager $livewire, Model $record)
                    {
                        // Runs after the form fields are saved to the database.
                        $livewire->dispatch('itemedited', $record->wishlist_id);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->after(function (RelationManager $livewire, Model $record)
                    {
                        // Runs after the form fields are saved to the database.
                        $livewire->dispatch('itemedited', $record->wishlist_id);
                    }),
            ]);
    }
}
