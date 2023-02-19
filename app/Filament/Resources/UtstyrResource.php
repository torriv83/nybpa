<?php

namespace App\Filament\Resources;

use Closure;
use Filament\Forms;
use Filament\Tables;
use App\Models\Utstyr;
use Filament\Resources\Form;
use Filament\Resources\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Mail;
use Filament\Forms\Components\Select;
use Illuminate\Support\Facades\Route;
use Filament\Forms\Components\Repeater;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use App\Mail\BestillUtstyr as Bestilling;
use Filament\Tables\Columns\SelectColumn;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\ReplicateAction;
use Filament\Tables\Columns\TextInputColumn;
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
    protected static ?int $navigationSort      = 4;
    protected static ?string $navigationIcon   = 'heroicon-o-collection';

    public static function getGloballySearchableAttributes(): array
    {
        return ['hva', 'navn', 'artikkelnummer', 'kategori.kategori'];
    }

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return $record->navn;
    }

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
                TextInputColumn::make('antall')->type('number')->extraAttributes(['style' => 'width:60px']),
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
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()->label('Se'),
                    Tables\Actions\EditAction::make()->label('Endre'),
                    Tables\Actions\DeleteAction::make()->label('Slett'),
                    Tables\Actions\ForceDeleteAction::make()->label('Tving sletting'),
                    Tables\Actions\RestoreAction::make()->label('Angre sletting'),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
                Tables\Actions\ForceDeleteBulkAction::make(),
                Tables\Actions\RestoreBulkAction::make(),
                BulkAction::make('bestillValgteProdukter')
                    ->action(function (Collection $records, array $data) {
                        Mail::to('tor1@trivera.net')->send(new Bestilling($records, $data));
                    })
                    ->form([
                        Forms\Components\Textarea::make('info')
                            ->label('Annen informasjon'),
                    ])
                    ->requiresConfirmation()
                    ->modalHeading('Bestill utstyr')
                    ->modalSubheading('Sikker på at du har valgt alt du trenger?')
                    ->modalButton('ja, bestill utstyr!')
                    ->deselectRecordsAfterCompletion()
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
