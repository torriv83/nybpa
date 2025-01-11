<?php

namespace App\Filament\Privat\Resources;

use App\Filament\Privat\Resources\ResepterResource\Pages;
use App\Filament\Privat\Resources\ResepterResource\RelationManagers;
use App\Models\Resepter;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class ResepterResource extends Resource
{
    protected static ?string $model = Resepter::class;

    protected static ?string $navigationIcon = 'icon-resept';

    protected static ?string $navigationGroup = 'Medisinsk';

    protected static ?string $modelLabel = 'Resept';

    protected static ?string $pluralModelLabel = 'Resepter';

    protected static ?int $navigationSort = 3;


    public static function getNavigationBadge(): ?string
    {

        return Cache::tags(['medisinsk'])->remember('ReseptNavigationBadge', now()->addMonth(), function () {
            return static::getModel()::count();
        });
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')->label('Navn på resept')->autofocus()->required(),
                Forms\Components\DatePicker::make('validTo')->label('Gyldig til')->native(false)->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Navn på resept'),
                Tables\Columns\TextColumn::make('validTo')->label('Gyldig til')->date('d.m.Y')->sortable(),
            ])
            ->recordClasses(fn(Model $record) => match (true) {
                $record->validTo < now() => '!border-x-2 !border-x-red-600 !dark:border-x-red-600',
                $record->validTo < now()->addMonth() => '!border-x-2 !border-x-yellow-600 !dark:border-x-yellow-600',
                default => '!border-x-2 !border-x-green-600 !dark:border-x-green-600',
            })
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListResepters::route('/'),
            'create' => Pages\CreateResepter::route('/create'),
            'edit' => Pages\EditResepter::route('/{record}/edit'),
        ];
    }
}
