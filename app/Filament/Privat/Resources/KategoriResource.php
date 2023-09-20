<?php

namespace App\Filament\Privat\Resources;

use App\Filament\Privat\Resources\KategoriResource\Pages\CreateKategori;
use App\Filament\Privat\Resources\KategoriResource\Pages\EditKategori;
use App\Filament\Privat\Resources\KategoriResource\Pages\ListKategoris;
use App\Filament\Privat\Resources\KategoriResource\Pages\ViewKategori;
use App\Models\Kategori;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Cache;

class KategoriResource extends Resource
{
    protected static ?string $model = Kategori::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Medisinsk';

    protected static ?string $modelLabel = 'Kategori';

    protected static ?string $pluralModelLabel = 'Kategorier';

    protected static ?int $navigationSort = 2;

    public static function getNavigationBadge(): ?string
    {

        return Cache::tags(['medisinsk'])->remember('CategoryNavigationBadge', now()->addMonth(), function () {
            return static::getModel()::count();
        });
    }

    public static function form(Form $form): Form
    {

        return $form
            ->schema([
                TextInput::make('kategori')
                    ->required()
                    ->maxLength(191)
                    ->autofocus(),
            ]);
    }

    /**
     * @throws \Exception
     */
    public static function table(Table $table): Table
    {

        return $table
            ->columns([
                TextColumn::make('kategori'),
                TextColumn::make('deleted_at')
                    ->dateTime(),
                TextColumn::make('created_at')
                    ->dateTime(),
                TextColumn::make('updated_at')
                    ->dateTime(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            'index' => ListKategoris::route('/'),
            'create' => CreateKategori::route('/create'),
            'view' => ViewKategori::route('/{record}'),
            'edit' => EditKategori::route('/{record}/edit'),
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
