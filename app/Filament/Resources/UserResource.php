<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Resources\Form;
use Filament\Resources\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Card;
use Illuminate\Support\Facades\Hash;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\ToggleColumn;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\DateTimePicker;
use App\Filament\Resources\UserResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\UserResource\RelationManagers;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;

class UserResource extends Resource
{
    protected static ?string $model            = User::class;
    protected static ?string $navigationGroup  = 'Authentication';
    protected static ?string $navigationIcon   = 'heroicon-o-users';
    protected static ?string $modelLabel       = 'Bruker';
    protected static ?string $pluralModelLabel = 'Brukere';
    protected static ?int $navigationSort      = 1;
    protected static ?string $recordTitleAttribute = 'name';

    protected static function getNavigationBadge(): ?string
    {
        return static::getModel()::role(['Fast ansatt', 'Tilkalling'])->count();
    }

    public static function form(Form $form): Form
    {
        // Section::make('Heading')
        //     ->description('Description')
        //     ->schema([
        //         // ...
        //     ])
        return $form
            ->schema([
                Section::make('Standard data')
                    ->description('')
                    ->schema([
                        TextInput::make('name')
                            ->label('Navn')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->label('E-post')
                            ->email()
                            ->required()
                            ->maxLength(255),
                        DateTimePicker::make('email_verified_at')
                            ->label('E-post verifisert'),
                        TextInput::make('password')
                            ->same('passwordConfirmation')
                            ->password()
                            ->maxLength(255)
                            ->required(fn ($component, $get, $livewire, $model, $record, $set, $state) => $record === null)
                            ->label('Passord'),
                        TextInput::make('passwordConfirmation')
                            ->password()
                            ->dehydrated(false)
                            ->maxLength(255)
                            ->label('Bekreft Passord'),
                        Select::make('roles')
                            ->required()
                            ->multiple()
                            ->relationship('roles', 'name')
                            ->preload(config('filament-authentication.preload_roles'))
                            ->label(strval(__('filament-authentication::filament-authentication.field.user.roles'))),
                    ])->columns(3),

                Section::make('Personlig data')
                    ->description('')
                    ->schema([
                        TextInput::make('phone')
                            ->tel()
                            ->label('Telefon')
                            ->maxLength(8),
                        TextInput::make('adresse')
                            ->maxLength(255),
                        TextInput::make('postnummer')
                            ->maxLength(4),
                        TextInput::make('poststed')
                            ->maxLength(255),
                        TextInput::make('assistentnummer')
                            ->maxLength(255),
                        DatePicker::make('ansatt_dato')
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->label('Navn'),
                Tables\Columns\TextColumn::make('email')
                    ->label('E-post'),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Telefon'),
                Tables\Columns\IconColumn::make('email_verified_at')
                    ->boolean()
                    ->label('Verified'),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->label('Slettet')
                    ->datetime('d.m.Y H:i:s'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Opprettet')
                    ->dateTime('d.m.Y H:i:s')->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Sist oppdatert')
                    ->dateTime('d.m.Y H:i:s')->sortable(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
                Tables\Filters\Filter::make('verified')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('email_verified_at')),
                Tables\Filters\Filter::make('unverified')
                    ->query(fn (Builder $query): Builder => $query->whereNull('email_verified_at')),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\ForceDeleteAction::make()->label('Tving sletting'),
                    Tables\Actions\RestoreAction::make()->label('Angre sletting'),
                ]),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
