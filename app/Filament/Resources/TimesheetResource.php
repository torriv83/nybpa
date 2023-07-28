<?php

/** @noinspection PhpUnused */

namespace App\Filament\Resources;

use App\Filament\Resources\TimesheetResource\Pages;
use App\Filament\Resources\TimesheetResource\Widgets\HoursUsedEachMonth;
use App\Models\Timesheet;
use App\Models\User;
use Carbon\Carbon;
use Closure;
use Exception;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class TimesheetResource extends Resource
{
    /**
     * Innstillinger
     */
    protected static ?string $model = Timesheet::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static ?string $navigationGroup = 'Tider';

    protected static ?string $modelLabel = 'Timeliste';

    protected static ?string $pluralModelLabel = 'Timelister';

    /**
     * Tabell
     *
     *
     * @throws Exception
     */
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
                    ->formatStateUsing(function ($record) {
                        if ($record->unavailable == 1) {
                            return Carbon::parse($record->fra_dato)->format('d.m.Y').' - '.Carbon::parse($record->til_dato)->format('d.m.Y');
                        } else {
                            return Carbon::parse($record->fra_dato)->format('d.m.Y');
                        }
                    })
                    ->searchable()
                    ->sortable()
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
                    ->getStateUsing(fn (Model $record) => ! is_null($record->description) ? strip_tags($record->description) : '')
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {

                        $state = $column->getState();

                        if (strlen($state) <= $column->getLimit()) {
                            return null;
                        }

                        return $state;
                    })
                    ->toggleable(),

                Tables\Columns\TextColumn::make('totalt')
                    ->getStateUsing(function (Model $record) {

                        if ($record->allDay) {
                            return '-';
                        } else {
                            $minutes = $record->totalt;

                            return sprintf('%02d', intdiv($minutes, 60)).':'.(sprintf('%02d', $minutes % 60));
                        }
                    })
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\IconColumn::make('unavailable')
                    ->label('Borte')
                    ->sortable()
                    ->boolean()
                    ->trueIcon('heroicon-o-badge-check')
                    ->falseIcon('heroicon-o-x-circle')
                    ->toggleable(),

                Tables\Columns\IconColumn::make('allDay')
                    ->label('Hele dagen?')
                    ->sortable()
                    ->boolean()
                    ->trueIcon('heroicon-o-badge-check')
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
                    ->query(fn (Builder $query): Builder => $query->where('unavailable', '=', '0'))->default(),

                Tables\Filters\Filter::make('Ikke tilgjengelig')
                    ->query(fn (Builder $query): Builder => $query->where('unavailable', '=', '1')),

                Tables\Filters\SelectFilter::make('assistent')
                    ->relationship('user', 'name', fn (Builder $query) => $query->permission('Assistent')),

                Tables\Filters\Filter::make('Forrige måned')
                    ->query(fn (Builder $query): Builder => $query
                        ->where('fra_dato', '<=', Carbon::now()->subMonth()->endOfMonth())
                        ->where('til_dato', '>=', Carbon::now()->subMonth()->startOfMonth())),

                Tables\Filters\Filter::make('Denne måneden')
                    ->query(fn (Builder $query): Builder => $query
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
                                fn (Builder $query, $date): Builder => $query->whereDate('til_dato', '>=', $date),
                            )
                            ->when(
                                $data['til_dato'],
                                fn (Builder $query, $date): Builder => $query->whereDate('fra_dato', '<=', $date),
                            );
                    }),
            ])

            //Actions
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()->label('Se')->slideOver(),
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
            ]);
    }

    /**
     * Skjema
     */
    public static function form(Form $form): Form
    {

        return $form
            ->schema([

                //Seksjon
                Forms\Components\Section::make('Assistent')
                    ->description('Velg assistent og om han/hun er tilgjengelig eller ikke, og om det gjelder hele dagen.')
                    ->schema([

                        Forms\Components\Select::make('user_id')
                            ->label('Hvem')
                            ->options(User::all()->filter(fn ($value) => $value->id != Auth::User()->id)->pluck('name',
                                'id'))
                            ->required()
                            ->columnSpan(2),

                        Forms\Components\Checkbox::make('unavailable')
                            ->label('Ikke Tilgjengelig?'),

                        Forms\Components\Checkbox::make('allDay')
                            ->label('Hele dagen?'),

                    ])->columns(),

                //Seksjon
                Forms\Components\Section::make('Tid')
                    ->description('Velg fra og til')
                    ->schema([

                        Forms\Components\DateTimePicker::make('fra_dato')
                            ->displayFormat('d.m.Y H:i')
                            ->required(),

                        Forms\Components\DateTimePicker::make('til_dato')
                            ->displayFormat('d.m.Y H:i')
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function (Closure $set, $state, $get) {

                                $fra = $get('fra_dato');
                                $set('totalt', Carbon::createFromFormat('Y-m-d H:i:s', $fra)->diffInMinutes($state));

                                $minutes = Carbon::createFromFormat('Y-m-d H:i:s', $fra)->diffInMinutes($state);
                                $hours = sprintf('%02d', intdiv($minutes, 60)).':'.(sprintf('%02d', $minutes % 60));
                                $set('Tid', $hours);
                            }),

                        Forms\Components\RichEditor::make('description')
                            ->label('Beskrivelse')
                            ->disableToolbarButtons([
                                'attachFiles',
                                'blockquote',
                                'codeBlock',
                                'h2',
                                'h3',
                                'link',
                                'redo',
                                'strike',
                            ])
                            ->maxLength(191),

                        Forms\Components\TextInput::make('Tid')
                            ->afterStateHydrated(function (Forms\Components\TextInput $component, $state, $get) {

                                if ($get('fra_dato')) {
                                    $fra = Carbon::createFromFormat('Y-m-d H:i:s',
                                        $get('fra_dato'))->diffInMinutes($get('til_dato'));
                                    $minutes = $fra;
                                    $hours = sprintf('%02d', intdiv($minutes, 60)).':'.(sprintf('%02d',
                                        $minutes % 60));
                                    $component->state($hours);
                                }
                            })
                            ->label('Total tid')
                            ->disabled(),

                        Forms\Components\Hidden::make('totalt'),

                    ])->columns(),
            ]);
    }

    public static function getRelations(): array
    {

        return [
            //
        ];
    }

    /**
     * Uten global scopes
     */
    public static function getEloquentQuery(): Builder
    {

        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    /**
     * Sider
     */
    public static function getPages(): array
    {

        return [
            'index' => Pages\ListTimesheets::route('/'),
            'create' => Pages\CreateTimesheet::route('/create'),
            // 'view'   => Pages\ViewTimesheet::route('/{record}'),
            'edit' => Pages\EditTimesheet::route('/{record}/edit'),
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
