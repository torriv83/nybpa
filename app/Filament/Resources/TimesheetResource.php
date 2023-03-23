<?php

namespace App\Filament\Resources;

use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction;
use App\Filament\Resources\TimesheetResource\Pages;
use App\Filament\Resources\TimesheetResource\Widgets\HoursUsedEachMonth;
use App\Models\Timesheet;
use App\Models\User;
use Carbon\Carbon;
use Closure;
use Exception;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ForceDeleteAction;
use Filament\Tables\Actions\ForceDeleteBulkAction;
use Filament\Tables\Actions\RestoreAction;
use Filament\Tables\Actions\RestoreBulkAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use function strip_tags;

class TimesheetResource extends Resource
{

    /**
     * Innstillinger
     */
    protected static ?string $model            = Timesheet::class;
    protected static ?string $navigationIcon   = 'heroicon-o-clock';
    protected static ?string $navigationGroup  = 'Tider';
    protected static ?string $modelLabel       = 'Timeliste';
    protected static ?string $pluralModelLabel = 'Timelister';

    protected function getDefaultTableSortColumn() : ?string
    {

        return 'fra_dato';
    }

    protected function getDefaultTableSortDirection() : ?string
    {

        return 'desc';
    }

    /**
     * Skjema
     *
     * @param  Form  $form
     *
     * @return Form
     */
    public static function form(Form $form) : Form
    {

        return $form
            ->schema([

                //Seksjon
                Section::make('Assistent')
                    ->description('Velg assistent og om han/hun er tilgjengelig eller ikke, og om det gjelder hele dagen.')
                    ->schema([

                        Select::make('user_id')
                            ->label('Hvem')
                            ->options(User::all()->filter(fn($value) => $value->id != Auth::User()->id)->pluck('name', 'id'))
                            ->required()
                            ->columnSpan(2),

                        Checkbox::make('unavailable')
                            ->label('Ikke Tilgjengelig?'),

                        Checkbox::make('allDay')
                            ->label('Hele dagen?'),

                    ])->columns(),

                //Seksjon
                Section::make('Tid')
                    ->description('Velg fra og til')
                    ->schema([

                        DateTimePicker::make('fra_dato')
                            ->displayFormat('d.m.Y H:i')
                            ->required(),

                        DateTimePicker::make('til_dato')
                            ->displayFormat('d.m.Y H:i')
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function (Closure $set, $state, $get) {

                                $fra = $get('fra_dato');
                                $set('totalt', Carbon::createFromFormat('Y-m-d H:i:s', $fra)->diffInMinutes($state));

                                $minutes = Carbon::createFromFormat('Y-m-d H:i:s', $fra)->diffInMinutes($state);
                                $hours   = sprintf('%02d', intdiv($minutes, 60)).':'.(sprintf('%02d', $minutes % 60));
                                $set('Tid', $hours);
                            }),

                        RichEditor::make('description')
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

                        TextInput::make('Tid')
                            ->afterStateHydrated(function (TextInput $component, $state, $get) {

                                if ($get('fra_dato')) {
                                    $fra     = Carbon::createFromFormat('Y-m-d H:i:s', $get('fra_dato'))->diffInMinutes($get('til_dato'));
                                    $minutes = $fra;
                                    $hours   = sprintf('%02d', intdiv($minutes, 60)).':'.(sprintf('%02d', $minutes % 60));
                                    $component->state($hours);
                                }
                            })
                            ->label('Total tid')
                            ->disabled(),

                        Hidden::make('totalt'),

                    ])->columns(),
            ]);
    }

    /**
     * Tabell
     *
     * @param  Table  $table
     *
     * @return Table
     * @throws Exception
     */
    public static function table(Table $table) : Table
    {

        return $table
            ->columns([

                TextColumn::make('user.name')
                    ->label('Assistent')
                    ->searchable(),

                TextColumn::make('fra_dato')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('til_dato')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('description')
                    ->label('Beskrivelse')
                    ->limit(20)
                    ->getStateUsing(function (Model $record) {

                        return strip_tags($record->description);
                    })
                    ->tooltip(function (TextColumn $column) : ?string {

                        $state = $column->getState();

                        if (strlen($state) <= $column->getLimit()) {
                            return null;
                        }

                        return $state;
                    })
                    ->toggleable(),

                TextColumn::make('totalt')
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

                IconColumn::make('unavailable')
                    ->label('Borte')
                    ->sortable()
                    ->boolean()
                    ->trueIcon('heroicon-o-badge-check')
                    ->falseIcon('heroicon-o-x-circle')
                    ->toggleable(),

                IconColumn::make('allDay')
                    ->label('Hele dagen?')
                    ->sortable()
                    ->boolean()
                    ->trueIcon('heroicon-o-badge-check')
                    ->falseIcon('heroicon-o-x-circle')
                    ->toggleable(),

                TextColumn::make('deleted_at')
                    ->label('Arkivert?')
                    ->sortable()
                    ->date('d.m.Y')
                    ->toggleable(),

            ])->defaultSort('fra_dato', 'desc')

            //Filtre
            ->filters([

                TrashedFilter::make(),

                Filter::make('Tilgjengelig')
                    ->query(fn(Builder $query) : Builder => $query->where('unavailable', '=', '0'))->default(),

                Filter::make('Ikke tilgjengelig')
                    ->query(fn(Builder $query) : Builder => $query->where('unavailable', '=', '1')),

                SelectFilter::make('assistent')
                    ->relationship('user', 'name', fn(Builder $query) => $query->permission('Assistent')),

                Filter::make('Forrige måned')
                    ->query(fn(Builder $query) : Builder => $query
                        ->where('fra_dato', '<=', Carbon::now()->subMonth()->endOfMonth())
                        ->where('til_dato', '>=', Carbon::now()->subMonth()->startOfMonth())),

                Filter::make('Denne måneden')
                    ->query(fn(Builder $query) : Builder => $query
                        ->where('fra_dato', '<=', Carbon::now()->endOfMonth())
                        ->where('til_dato', '>=', Carbon::now()->startOfMonth()))->default(),

                Filter::make('måned')
                    ->form([
                        DatePicker::make('fra_dato'),
                        DatePicker::make('til_dato'),
                    ])
                    ->query(function (Builder $query, array $data) : Builder {

                        return $query
                            ->when(
                                $data['fra_dato'],
                                fn(Builder $query, $date) : Builder => $query->whereDate('til_dato', '>=', $date),
                            )
                            ->when(
                                $data['til_dato'],
                                fn(Builder $query, $date) : Builder => $query->whereDate('fra_dato', '<=', $date),
                            );
                    })
            ])

            //Actions
            ->actions([
                ActionGroup::make([
                    ViewAction::make()->label('Se'),
                    EditAction::make()->label('Endre'),
                    DeleteAction::make()->label('Slett'),
                    ForceDeleteAction::make()->label('Tving sletting'),
                    RestoreAction::make()->label('Angre sletting'),
                ]),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
                ForceDeleteBulkAction::make(),
                RestoreBulkAction::make(),
                FilamentExportBulkAction::make('export'),
            ]);
    }

    public static function getRelations() : array
    {

        return [
            //
        ];
    }

    /**
     * Uten global scopes
     *
     * @return Builder
     */
    public static function getEloquentQuery() : Builder
    {

        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    /**
     * Sider
     *
     * @return array
     */
    public static function getPages() : array
    {

        return [
            'index'  => Pages\ListTimesheets::route('/'),
            'create' => Pages\CreateTimesheet::route('/create'),
            // 'view'   => Pages\ViewTimesheet::route('/{record}'),
            'edit'   => Pages\EditTimesheet::route('/{record}/edit'),
        ];
    }

    public static function getWidgets() : array
    {

        return [
            HoursUsedEachMonth::class,
        ];
    }
}
