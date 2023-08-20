<?php

namespace App\Filament\Assistent\Widgets;

use App\Models\Timesheet;
use Carbon\Carbon;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;

class UnavailableTable extends BaseWidget
{
    protected static ?string $heading         = 'Tider satt som ikke tilgjengelig';
    protected static ?string $pollingInterval = null;
    protected static ?int    $sort            = 3;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Timesheet::query()->where('user_id', Auth::user()->id)->where('unavailable', '=', 1)->inFuture('fra_dato')
            )
            ->columns([
                TextColumn::make('fra_dato')->dateTime('d.m.Y, H:i')->sortable(),
                TextColumn::make('til_dato')->dateTime('d.m.Y, H:i')->sortable(),
            ])->headerActions([
                CreateAction::make()->label('Legg til tid du ikke kan jobbe')
                    ->form([
                        //Seksjon
                        Section::make(fn() => Auth::user()->name)
                            ->description('Velg om det gjelder hele dagen eller ikke')
                            ->schema([
                                Checkbox::make('allDay')
                                    ->label('Hele dagen?')->live()
                            ])->columns(),

                        //Seksjon
                        Section::make('Tid')
                            ->description('Velg fra og til.')
                            ->schema([

                                DateTimePicker::make('fra_dato')
                                    ->displayFormat('d.m.Y')
                                    ->seconds(false)
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(fn(Set $set, ?string $state) => $set('til_dato',
                                        Carbon::parse($state)->addHour()->format('Y-m-d H:i')))
                                    ->hidden(fn(Get $get): bool => $get('allDay')),
                                DateTimePicker::make('til_dato')
                                    ->displayFormat('d.m.Y')->seconds(false)
                                    ->required()
                                    ->hidden(fn(Get $get): bool => $get('allDay')),

                                DatePicker::make('fra_dato')
                                    ->displayFormat('d.m.Y')
                                    ->required()
                                    ->hidden(fn(Get $get): bool => !$get('allDay')),
                                DatePicker::make('til_dato')
                                    ->displayFormat('d.m.Y')
                                    ->required()
                                    ->hidden(fn(Get $get): bool => !$get('allDay')),

                                RichEditor::make('description')
                                    ->label('Begrunnelse (Valgfritt)')
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
                                    ->maxLength(255)->columnSpanFull(),

                                Hidden::make('totalt')->default(0),
                                Hidden::make('unavailable')->default(true),
                                Hidden::make('user_id')->default(Auth::user()->id),

                            ])->columns(),
                    ]),
            ]);
    }
}
