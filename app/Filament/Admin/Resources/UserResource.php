<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\UserResource\Pages;
use App\Models\User;
use Exception;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components\Section as InfoSection;
use Filament\Infolists\Components\Split;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use STS\FilamentImpersonate\Tables\Actions\Impersonate;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationGroup = 'Authentication';

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $modelLabel = 'Bruker';

    protected static ?string $pluralModelLabel = 'Brukere';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
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
                            ->dehydrateStateUsing(fn(string $state): string => Hash::make($state))
                            ->dehydrated(fn(?string $state): bool => filled($state))
                            ->required(fn(string $operation): bool => $operation === 'create')
                            ->maxLength(255)
                            ->required(fn($record): string => $record === null)
                            ->label('Passord'),
                        TextInput::make('passwordConfirmation')
                            ->password()
                            ->dehydrated(false)
                            ->maxLength(255)
                            ->label('Bekreft Passord'),
                        Select::make('roles')
                            ->label('Rolle')
                            ->required()
                            ->multiple()
                            ->relationship('roles', 'name')
                            ->preload()
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
                        DatePicker::make('ansatt_dato'),
                    ])->columns(3),
            ]);
    }

    /**
     * @throws Exception
     */
    public static function table(Table $table): Table
    {
        return $table
            ->query(
                User::query()->assistenter()
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->label('Navn')
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('E-post'),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Telefon'),
                Tables\Columns\IconColumn::make('email_verified_at')
                    ->boolean()
                    ->sortable()
                    ->label('Verified')->alignCenter(),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->label('Slettet')
                    ->datetime('d.m.Y H:i'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Opprettet')
                    ->dateTime('d. M Y, H:i')->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Sist oppdatert')
                    ->dateTime('d. M Y, H:i')->sortable(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
                Tables\Filters\Filter::make('verified')
                    ->query(fn(Builder $query): Builder => $query->whereNotNull('email_verified_at')),
                Tables\Filters\Filter::make('unverified')
                    ->query(fn(Builder $query): Builder => $query->whereNull('email_verified_at')),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->slideOver(),
                Impersonate::make()->redirectTo(route('filament.assistent.pages.dashboard')),
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
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

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Split::make([
                    InfoSection::make([
                        TextEntry::make('name')->label('Navn'),
                        TextEntry::make('email')->label('E-post'),
                        TextEntry::make('phone')->label('Tlf'),
                        TextEntry::make('adresse'),
                        TextEntry::make('postnummer'),
                        TextEntry::make('poststed'),
                    ])->grow()->columns(),
                    InfoSection::make([
                        TextEntry::make('roles.name')->label('Rolle'),
                        TextEntry::make('created_at')->label('Opprettet')->dateTime('d.m.Y H:i'),
                        TextEntry::make('updated_at')->label('Oppdatert')->dateTime('d.m.Y H:i'),
                    ])->columns(),
                ])->from('md'),
            ])->columns(1);
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
            'index'  => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            //'view'   => Pages\ViewUser::route('/{record}'),
            'edit'   => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return Cache::tags(['bruker'])->remember('UserNavigationBadge', now()->addMonth(), function ()
        {
            $roles = ['Fast ansatt', 'Tilkalling'];

            // Check if any of the roles exist in the database
            $rolesExist = Role::whereIn('name', $roles)->exists();

            return !$rolesExist ? app(static::getModel())->count() : app(static::getModel())->role($roles)->count();
        });
    }
}
