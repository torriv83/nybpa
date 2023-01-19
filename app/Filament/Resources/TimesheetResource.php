<?php

namespace App\Filament\Resources;

use Closure;
use Carbon\Carbon;
use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use App\Models\Timesheet;
use Filament\Resources\Form;
use Filament\Resources\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Section;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\TimesheetResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\TimesheetResource\RelationManagers;

class TimesheetResource extends Resource
{

    protected static ?string $model            = Timesheet::class;
    protected static ?string $navigationIcon   = 'heroicon-o-collection';
    protected static ?string $navigationGroup  = 'Tider';
    protected static ?string $modelLabel       = 'Timeliste';
    protected static ?string $pluralModelLabel = 'Timelister';

    protected function getDefaultTableSortColumn(): ?string
    {
        return 'fra_dato';
    }

    protected function getDefaultTableSortDirection(): ?string
    {
        return 'desc';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Assistent')
                    ->description('Velg assistent og om han/hun er tilgjengelig eller ikke, og om det gjelder hele dagen.')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('Hvem')
                            ->options(User::all()->filter(function ($value, $key) {
                                return $value->id != Auth::User()->id;
                            })->pluck('name', 'id'))
                            ->required()
                            ->columnSpan(2),
                        Forms\Components\Checkbox::make('unavailable')
                            ->label('Ikke Tilgjengelig?'),
                        Forms\Components\Checkbox::make('allDay')
                            ->label('Hele dagen?'),
                    ])->columns(2),
                Section::make('Tid')
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
                                $hours = sprintf('%02d', intdiv($minutes, 60)) . ':' . (sprintf('%02d', $minutes % 60));
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
                                    $fra = Carbon::createFromFormat('Y-m-d H:i:s', $get('fra_dato'))->diffInMinutes($get('til_dato'));
                                    $minutes = $fra;
                                    $hours = sprintf('%02d', intdiv($minutes, 60)) . ':' . (sprintf('%02d', $minutes % 60));
                                    $component->state($hours);
                                }
                            })
                            ->label('Total tid')
                            ->disabled(),
                        Forms\Components\Hidden::make('totalt'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Assistent')
                    ->searchable(),
                Tables\Columns\TextColumn::make('fra_dato')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('til_dato')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
                    ->label('Beskrivelse')
                    ->limit(20)
                    ->getStateUsing(function (Model $record) {
                        return \strip_tags($record->description);
                    })
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();

                        if (strlen($state) <= $column->getLimit()) {
                            return null;
                        }

                        return $state;
                    }),
                Tables\Columns\TextColumn::make('totalt')
                    ->getStateUsing(function (Model $record) {
                        if ($record->allDay) {
                            return '-';
                        } else {
                            $minutes = $record->totalt;
                            $hours = sprintf('%02d', intdiv($minutes, 60)) . ':' . (sprintf('%02d', $minutes % 60));
                            return $hours;
                        }
                    })
                    ->sortable(),
                Tables\Columns\IconColumn::make('unavailable')
                    ->label('Borte')
                    ->sortable()
                    ->boolean()
                    ->trueIcon('heroicon-o-badge-check')
                    ->falseIcon('heroicon-o-x-circle'),
                Tables\Columns\IconColumn::make('allDay')
                    ->label('Hele dagen?')
                    ->sortable()
                    ->boolean()
                    ->trueIcon('heroicon-o-badge-check')
                    ->falseIcon('heroicon-o-x-circle'),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->label('Arkivert?')
                    ->sortable()
                    ->date('d.m.Y'),
            ])->defaultSort('fra_dato', 'desc')
            ->filters([
                Tables\Filters\TrashedFilter::make(),
                Tables\Filters\Filter::make('Tilgjengelig')
                    ->query(fn (Builder $query): Builder => $query->where('unavailable', '=', '0')),
                Tables\Filters\Filter::make('Ikke tilgjengelig')
                    ->query(fn (Builder $query): Builder => $query->where('unavailable', '=', '1')),
                Tables\Filters\SelectFilter::make('assistent')
                    ->relationship('user', 'name', fn (Builder $query) => $query->permission('Assistent')),
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
                    })
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
}
