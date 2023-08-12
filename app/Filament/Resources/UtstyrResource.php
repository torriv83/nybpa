<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UtstyrResource\Pages;
use App\Mail\BestillUtstyr as Bestilling;
use App\Models\Settings;
use App\Models\Utstyr;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;

class UtstyrResource extends Resource
{
    protected static ?string $model = Utstyr::class;

    protected static ?string $navigationGroup = 'Medisinsk';

    protected static ?string $modelLabel = 'Utstyr';

    protected static ?string $pluralModelLabel = 'Utstyr';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getGloballySearchableAttributes(): array
    {
        return ['hva', 'navn', 'artikkelnummer', 'kategori.kategori'];
    }

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return $record->navn;
    }

    /**
     * @throws \Exception
     */
    public static function table(Table $table): Table
    {

        return $table
            ->columns([
                Tables\Columns\TextInputColumn::make('antall')->type('number')->extraAttributes(['style' => 'width:80px']),
                Tables\Columns\TextColumn::make('hva')->sortable(),
                Tables\Columns\TextColumn::make('navn')->sortable(),
                Tables\Columns\TextColumn::make('kategori.kategori')->sortable(),
                Tables\Columns\TextColumn::make('artikkelnummer'),
                Tables\Columns\TextColumn::make('link')
                    ->formatStateUsing(fn() => 'Se her')
                    ->url(fn($record) => $record->link, true),
            ])->striped()
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
                Tables\Actions\BulkAction::make('bestillValgteProdukter')
                    ->action(function (Collection $records, array $data) {
                        $setting = Settings::where('user_id', '=', Auth::id())->first();
                        Mail::to($setting->apotek_epost)->send(new Bestilling($records, $data));
                        Notification::make()
                            ->title('E-post har blitt sendt')
                            ->success()
                            ->send();
                    })
                    ->form([
                        Forms\Components\Textarea::make('info')
                            ->label('Annen informasjon'),
                    ])
                    ->requiresConfirmation()
                    ->modalHeading('Bestill utstyr')
                    ->modalSubheading('Sikker på at du har valgt alt du trenger?')
                    ->modalContent(fn($records) => view('filament.pages.modalUtstyr', ['records' => $records]))
                    ->modalButton('Ja, bestill utstyr!')
                    ->deselectRecordsAfterCompletion()->modalWidth('lg'),
            ]);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('hva'),
                Forms\Components\TextInput::make('navn'),
                Forms\Components\TextInput::make('artikkelnummer'),
                Forms\Components\TextInput::make('link'),
                Forms\Components\Select::make('kategori')
                    ->relationship('kategori', 'kategori'),
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

    public static function getNavigationBadge(): ?string
    {
        return Cache::remember('UtstyrNavigationBadge', now()->addMonth(), function () {
            return static::getModel()::count();
        });
    }
}
