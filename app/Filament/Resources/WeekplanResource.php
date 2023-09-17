<?php
/**
 * Created by ${USER}.
 * Date: 18.04.2023
 * Time: 06.45
 * Company: Rivera Consulting
 */

namespace App\Filament\Resources;

use App\Filament\Resources\WeekplanResource\Pages;
use App\Models\Day;
use App\Models\Exercise;
use App\Models\Weekplan;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class WeekplanResource extends Resource
{
    protected static ?string $model = Weekplan::class;

    protected static ?string $slug = 'weekplans';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Landslag';

    protected static ?string $modelLabel = 'Ukeplan';

    protected static ?string $pluralModelLabel = 'Ukeplaner';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Fieldset::make('Ukeplan')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('name')
                                    ->required(),
                                Placeholder::make('created_at')
                                    ->label('Created Date')
                                    ->content(fn(?Weekplan $record): string => $record?->created_at?->diffForHumans() ?? '-'),

                                Placeholder::make('updated_at')
                                    ->label('Last Modified Date')
                                    ->content(fn(?Weekplan $record): string => $record?->updated_at?->diffForHumans() ?? '-'),
                            ]),

                    ]),

                Fieldset::make('Treninger')
                    ->schema([
                        Grid::make(['default' => 1])
                            ->schema([
                                Repeater::make('data')->label('Dager')
                                    ->schema([
                                        Select::make('day')->options(Day::all()->pluck('name', 'name'))->label('Dag'),
                                        Repeater::make('exercises')->label('Treninger')
                                            ->schema([
                                                Grid::make()
                                                    ->schema([
                                                        Select::make('exercise')->options(Exercise::all()->pluck('name', 'name'))->label('Øvelse'),
                                                        Select::make('intensity')->options([
                                                            'green'    => 'Lett',
                                                            'darkcyan' => 'Vedlikehold',
                                                            'crimson'  => 'Tung',
                                                        ])->label('Hvor tung?'),
                                                        TimePicker::make('from')->seconds(false)->label('Fra'),
                                                        TimePicker::make('to')->seconds(false)->label('Til'),
                                                    ]),
                                            ])
                                            ->itemLabel(fn (array $state): ?string => $state['exercise'] ?? null)
                                            ->addActionLabel('Legg til øvelse')
                                            ->collapsible()->collapsed(),
                                    ])
                                    ->itemLabel(fn (array $state): ?string => $state['day'] ?? null)
                                    ->addActionLabel('Legg til dag')
                                    ->collapsible()
                                    ->maxItems(7)
                                    ->grid(3),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime('d.m.y H:i:s')
                    ->label('Opprettet'),
                TextColumn::make('updated_at')
                    ->dateTime('d.m.y H:i:s')
                    ->label('Sist oppdatert'),
            ])->actions([
                Actions\EditAction::make(),
                Actions\ViewAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListWeekplans::route('/'),
            'create' => Pages\CreateWeekplan::route('/create'),
            'edit'   => Pages\EditWeekplan::route('/{record}/edit'),
//            'view'   => Pages\ViewWeekplan::route('/{record}'),
            'view'   => Pages\ViewWeekplan::route('/{record}'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name'];
    }
}
