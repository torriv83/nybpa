<?php

/** @noinspection PhpUnused */

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\TimesheetResource\Pages;
use App\Filament\Admin\Resources\TimesheetResource\Widgets\HoursUsedEachMonth;
use App\Models\Timesheet;
use App\Models\User;
use App\Traits\DateAndTimeHelper;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\Summarizers\Average;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TimesheetResource extends Resource
{
    use DateAndTimeHelper;

    /**
     * Innstillinger
     */
    protected static ?string $model = Timesheet::class;

    protected static ?string $navigationIcon = 'icon-schedule';

    protected static ?string $navigationGroup = 'Tider';

    protected static ?string $modelLabel = 'Timeliste';

    protected static ?string $pluralModelLabel = 'Timelister';

    protected static ?string $slug = 'timelister';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Assistent')
                    ->color(function ($record) {
                        if ($record->unavailable == 1) {
                            return 'danger';
                        } else {
                            return 'success';
                        }
                    })
                    ->searchable(),

                Tables\Columns\TextColumn::make('dato')
                    ->label('Dato')
                    ->getStateUsing(function (Model $record) {
                        if ($record->unavailable == 1) {
                            return Carbon::parse($record->fra_dato)->format('d.m.Y').' - '.Carbon::parse($record->til_dato)->format('d.m.Y');
                        } else {
                            return Carbon::parse($record->fra_dato)->format('d.m.Y');
                        }
                    })
                    ->searchable()
                    ->sortable(['fra_dato'])
                    ->toggleable(),

                Tables\Columns\TextColumn::make('fra_dato')
                    ->label('Fra')
                    ->dateTime('H:i')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('til_dato')
                    ->label('Til')
                    ->dateTime('H:i')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('description')
                    ->label('Beskrivelse')
                    ->limit(20)
                    ->getStateUsing(fn(Model $record) => !is_null($record->description) ? strip_tags($record->description) : '')
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();

                        if ($state === null || strlen($state) <= $column->getCharacterLimit()) {
                            // Handle the case where $state is null, e.g., return null or throw an exception.
                            return null;
                        }

                        // Only render the tooltip if the column content exceeds the length limit.
                        return $state;
                    })
                    ->toggleable(),

                Tables\Columns\TextColumn::make('totalt')
                    ->getStateUsing(function (Model $record) {
                        if ($record->unavailable) {
                            return '-';
                        } else {
                            $minutes = $record->totalt;

                            return sprintf('%02d', intdiv($minutes, 60)).':'.(sprintf('%02d', $minutes % 60));
                        }
                    })
                    ->sortable()
                    ->toggleable()
                    ->summarize([
                        Sum::make()->formatStateUsing(function (string $state) {
                            $minutes = $state;
                            return sprintf('%02d', intdiv($minutes, 60)).':'.(sprintf('%02d', $minutes % 60));
                        }),
                        Average::make()->formatStateUsing(function (string $state) {
                            $minutes = (int) floatval($state);
                            return sprintf('%02d', intdiv($minutes, 60)).':'.(sprintf('%02d', $minutes % 60));
                        })
                    ]),

                Tables\Columns\IconColumn::make('unavailable')
                    ->label('Borte')
                    ->sortable()
                    ->boolean()
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-x-circle')
                    ->toggleable(),

                Tables\Columns\IconColumn::make('allDay')
                    ->label('Hele dagen?')
                    ->sortable()
                    ->boolean()
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-x-circle')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('deleted_at')
                    ->label('Arkivert?')
                    ->sortable()
                    ->date('d.m.Y')
                    ->toggleable(),

            ])->defaultSort('fra_dato', 'desc')

            //Filtre
            ->filters([

                Tables\Filters\TrashedFilter::make(),

                Tables\Filters\Filter::make('Tilgjengelig')
                    ->query(fn(Builder $query): Builder => $query->where('unavailable', '0'))->default(),

                Tables\Filters\Filter::make('Ikke tilgjengelig')
                    ->query(fn(Builder $query): Builder => $query->where('unavailable', '1')),

                Tables\Filters\SelectFilter::make('assistent')
                    ->relationship('user', 'name', fn(Builder $query) => $query->permission('Assistent')),

                Tables\Filters\Filter::make('Forrige måned')
                    ->query(fn(Builder $query): Builder => $query
                        ->where('fra_dato', '<=', Carbon::now()->subMonth()->endOfMonth())
                        ->where('til_dato', '>=', Carbon::now()->subMonth()->startOfMonth())),

                Tables\Filters\Filter::make('Denne måneden')
                    ->query(fn(Builder $query): Builder => $query
                        ->where('fra_dato', '<=', Carbon::now()->endOfMonth())
                        ->where('til_dato', '>=', Carbon::now()->startOfMonth()))->default(),

                Tables\Filters\Filter::make('måned')
                    ->form([
                        Forms\Components\DatePicker::make('fra_dato'),
                        Forms\Components\DatePicker::make('til_dato'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['fra_dato'],
                                fn(Builder $query, $date): Builder => $query->whereDate('til_dato', '>=', $date),
                            )
                            ->when(
                                $data['til_dato'],
                                fn(Builder $query, $date): Builder => $query->whereDate('fra_dato', '<=', $date),
                            );
                    }),
            ])

            //Actions
            ->actions([
                Tables\Actions\ViewAction::make()->label('Se'),
                Tables\Actions\EditAction::make()->label('Endre'),
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\DeleteAction::make()->label('Slett'),
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

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Section
                Forms\Components\Section::make('Assistent')
                    ->description('Velg assistent og om han/hun er tilgjengelig eller ikke, og om det gjelder hele dagen.')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('Hvem')
                            ->options(User::query()->assistenter()->pluck('name', 'id'))
                            ->required()
                            ->live()
                            ->columnSpan(2),

                        Forms\Components\Checkbox::make('unavailable')
                            ->label('Ikke Tilgjengelig?'),

                        Forms\Components\Checkbox::make('allDay')
                            ->label('Hele dagen?')->live(),

                    ])->columns(),

                // Section
                Forms\Components\Section::make('Tid')
                    ->description('Velg fra og til')
                    ->schema([
                        ...self::getCommonFields(true),
                        TextInput::make('totalt')
                            ->label('Total tid')
                            ->disabled()
                            ->dehydrated(),
                    ])->columns(),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Assistent')
                    ->schema([
                        Infolists\Components\TextEntry::make('user.name')
                            ->label('Assistent'),
                        Infolists\Components\IconEntry::make('unavailable')
                            ->boolean()
                            ->label('Ikke tilgjengelig?'),
                        Infolists\Components\IconEntry::make('allDay')
                            ->boolean()
                            ->label('Hele dagen?'),
                    ])->columns(3),
                Section::make('Tid')
                    ->schema([
                        Infolists\Components\TextEntry::make('fra_dato')->dateTime('d.m.Y H:i'),
                        Infolists\Components\TextEntry::make('til_dato')->dateTime('d.m.Y H:i'),
                        Infolists\Components\TextEntry::make('totalt')
                            ->formatStateUsing(function (string $state) {
                                $minutes = $state;
                                return sprintf('%02d', intdiv($minutes, 60)).':'.(sprintf('%02d', $minutes % 60));
                            }),
                        Infolists\Components\TextEntry::make('description')
                            ->html()
                            ->label('Beskrivelse')
                            ->columnSpanFull(),
                    ])->columns(3),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListTimesheets::route('/'),
            'create' => Pages\CreateTimesheet::route('/create'),
            // 'view'   => Pages\ViewTimesheet::route('/{record}'),
            'edit'   => Pages\EditTimesheet::route('/{record}/edit'),
        ];
    }

    public static function getWidgets(): array
    {
        return [
            HoursUsedEachMonth::class,
        ];
    }

    protected function getDefaultTableSortColumn(): ?string
    {
        return 'fra_dato';
    }

    protected function getDefaultTableSortDirection(): ?string
    {
        return 'desc';
    }


}
