<?php

namespace App\Filament\Landslag\Resources;

use App\Filament\Landslag\Resources\WeekplanResource\Pages;
use App\Filament\Landslag\Resources\WeekplanResource\RelationManagers;
use App\Models\Exercise;
use App\Models\TrainingProgram;
use App\Models\Weekplan;
use App\Models\WeekplanExercise;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class WeekplanResource extends Resource
{
    protected static ?string $model = Weekplan::class;

    protected static ?string $slug = 'weekplans';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationGroup = 'Ukeplan';

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
                                    ->label('Navn')
                                    ->required(),
                                Placeholder::make('created_at')
                                    ->label('Opprettet')
                                    ->content(fn(?Weekplan $record): string => $record?->created_at?->diffForHumans() ?? '-'),
                                Placeholder::make('updated_at')
                                    ->label('Endret')
                                    ->content(fn(?Weekplan $record): string => $record?->updated_at?->diffForHumans() ?? '-'),
                            ]),
                    ]),
                Fieldset::make('Treninger')
                    ->schema([
                        Grid::make(['default' => 1])
                            ->schema([
                                Tabs::make('Label')
                                    ->tabs([
                                        Tabs\Tab::make('Mandag')
                                            ->badge(fn($record) => WeekplanExercise::where('day', 1)->where('weekplan_id', $record?->id)->count())
                                            ->schema([
                                                Repeater::make('exercises_1')
                                                    ->relationship('weekplanExercises', function ($query) {
                                                        $query->where('day', 1);
                                                    })
                                                    ->label('Økter')
                                                    ->schema([
                                                        Hidden::make('day')->default('1'),
                                                        Select::make('exercise_id')
                                                            ->options(Exercise::all()->pluck('name', 'id'))
                                                            ->label('Øvelse')
                                                            ->live(onBlur: true),
                                                        Select::make('training_program_id')
                                                            ->label('Velg styrkeprogram')
                                                            ->options(TrainingProgram::all()->pluck('program_name', 'id'))
                                                        ->hidden(function ($get) {
                                                            $exerciseName = Exercise::find($get('exercise_id'))->name ?? null;
                                                            return $exerciseName !== 'Styrketrening';
                                                        }),
                                                        TimePicker::make('start_time')
                                                            ->seconds(false)
                                                            ->label('Start'),
                                                        TimePicker::make('end_time')
                                                            ->seconds(false)
                                                            ->label('Slutt'),
                                                        Select::make('intensity')->options([
                                                            'green'    => 'Lett',
                                                            'darkcyan' => 'Vedlikehold',
                                                            'crimson'  => 'Tung',
                                                        ])->label('Hvor tung?'),
                                                    ])
                                                    ->defaultItems(0)
                                                    ->grid(4)
                                                    ->itemLabel(
                                                        fn(array $state): ?string => Exercise::all()->where('id', $state['exercise_id'])->first()?->name
                                                    )
                                                    ->addActionLabel('Legg til økt')
                                                    ->collapsible(),
                                            ]),
                                        Tabs\Tab::make('Tirsdag')
                                            ->badge(fn($record) => WeekplanExercise::where('day', 2)->where('weekplan_id', $record?->id)->count())
                                            ->schema([
                                                Repeater::make('exercises_2')
                                                    ->relationship('weekplanExercises', function ($query) {
                                                        $query->where('day', 2);
                                                    })
                                                    ->label('Økter')
                                                    ->schema([
                                                        Hidden::make('day')->default('2'),
                                                        Select::make('exercise_id')
                                                            ->options(Exercise::all()->pluck('name', 'id'))
                                                            ->label('Øvelse')
                                                            ->live(onBlur: true),
                                                        TimePicker::make('start_time')
                                                            ->seconds(false)
                                                            ->label('Start'),
                                                        TimePicker::make('end_time')
                                                            ->seconds(false)
                                                            ->label('Slutt'),
                                                        Select::make('intensity')->options([
                                                            'green'    => 'Lett',
                                                            'darkcyan' => 'Vedlikehold',
                                                            'crimson'  => 'Tung',
                                                        ])->label('Hvor tung?'),
                                                    ])
                                                    ->defaultItems(0)
                                                    ->grid(4)
                                                    ->itemLabel(
                                                        fn(array $state): ?string => Exercise::all()->where('id', $state['exercise_id'])->first()?->name
                                                    )
                                                    ->addActionLabel('Legg til økt')
                                                    ->collapsible(),
                                                ]),
                                        Tabs\Tab::make('Onsdag')
                                            ->badge(fn($record) => WeekplanExercise::where('day', 3)->where('weekplan_id', $record?->id)->count())
                                            ->schema([
                                                Repeater::make('exercises_3')
                                                    ->relationship('weekplanExercises', function ($query) {
                                                        $query->where('day', 3);
                                                    })
                                                    ->label('Økter')
                                                    ->schema([
                                                        Hidden::make('day')->default('3'),
                                                        Select::make('exercise_id')
                                                            ->options(Exercise::all()->pluck('name', 'id'))
                                                            ->label('Øvelse')
                                                            ->live(onBlur: true),
                                                        TimePicker::make('start_time')
                                                            ->seconds(false)
                                                            ->label('Start'),
                                                        TimePicker::make('end_time')
                                                            ->seconds(false)
                                                            ->label('Slutt'),
                                                        Select::make('intensity')->options([
                                                            'green'    => 'Lett',
                                                            'darkcyan' => 'Vedlikehold',
                                                            'crimson'  => 'Tung',
                                                        ])->label('Hvor tung?'),
                                                    ])
                                                    ->defaultItems(0)
                                                    ->grid(4)
                                                    ->itemLabel(
                                                        fn(array $state): ?string => Exercise::all()->where('id', $state['exercise_id'])->first()?->name
                                                    )
                                                    ->addActionLabel('Legg til økt')
                                                    ->collapsible(),
                                            ]),
                                        Tabs\Tab::make('Torsdag')
                                            ->badge(fn($record) => WeekplanExercise::where('day', 4)->where('weekplan_id', $record?->id)->count())
                                            ->schema([
                                                Repeater::make('exercises_4')
                                                    ->relationship('weekplanExercises', function ($query) {
                                                        $query->where('day', 4);
                                                    })
                                                    ->label('Økter')
                                                    ->schema([
                                                        Hidden::make('day')->default('4'),
                                                        Select::make('exercise_id')
                                                            ->options(Exercise::all()->pluck('name', 'id'))
                                                            ->label('Øvelse')
                                                            ->live(onBlur: true),
                                                        TimePicker::make('start_time')
                                                            ->seconds(false)
                                                            ->label('Start'),
                                                        TimePicker::make('end_time')
                                                            ->seconds(false)
                                                            ->label('Slutt'),
                                                        Select::make('intensity')->options([
                                                            'green'    => 'Lett',
                                                            'darkcyan' => 'Vedlikehold',
                                                            'crimson'  => 'Tung',
                                                        ])->label('Hvor tung?'),
                                                    ])
                                                    ->defaultItems(0)
                                                    ->grid(4)
                                                    ->itemLabel(
                                                        fn(array $state): ?string => Exercise::all()->where('id', $state['exercise_id'])->first()?->name
                                                    )
                                                    ->addActionLabel('Legg til økt')
                                                    ->collapsible(),
                                            ]),
                                        Tabs\Tab::make('Fredag')
                                            ->badge(fn($record) => WeekplanExercise::where('day', 5)->where('weekplan_id', $record?->id)->count())
                                            ->schema([
                                                Repeater::make('exercises_5')
                                                    ->relationship('weekplanExercises', function ($query) {
                                                        $query->where('day', 5);
                                                    })
                                                    ->label('Økter')
                                                    ->schema([
                                                        Hidden::make('day')->default('5'),
                                                        Select::make('exercise_id')
                                                            ->options(Exercise::all()->pluck('name', 'id'))
                                                            ->label('Øvelse')
                                                            ->live(onBlur: true),
                                                        TimePicker::make('start_time')
                                                            ->seconds(false)
                                                            ->label('Start'),
                                                        TimePicker::make('end_time')
                                                            ->seconds(false)
                                                            ->label('Slutt'),
                                                        Select::make('intensity')->options([
                                                            'green'    => 'Lett',
                                                            'darkcyan' => 'Vedlikehold',
                                                            'crimson'  => 'Tung',
                                                        ])->label('Hvor tung?'),
                                                    ])
                                                    ->defaultItems(0)
                                                    ->grid(4)
                                                    ->itemLabel(
                                                        fn(array $state): ?string => Exercise::all()->where('id', $state['exercise_id'])->first()?->name
                                                    )
                                                    ->addActionLabel('Legg til økt')
                                                    ->collapsible(),
                                            ]),
                                        Tabs\Tab::make('Lørdag')
                                            ->badge(fn($record) => WeekplanExercise::where('day', 6)->where('weekplan_id', $record?->id)->count())
                                            ->schema([
                                                Repeater::make('exercises_6')
                                                    ->relationship('weekplanExercises', function ($query) {
                                                        $query->where('day', 6);
                                                    })
                                                    ->label('Økter')
                                                    ->schema([
                                                        Hidden::make('day')->default('6'),
                                                        Select::make('exercise_id')
                                                            ->options(Exercise::all()->pluck('name', 'id'))
                                                            ->label('Øvelse')
                                                            ->live(onBlur: true),
                                                        TimePicker::make('start_time')
                                                            ->seconds(false)
                                                            ->label('Start'),
                                                        TimePicker::make('end_time')
                                                            ->seconds(false)
                                                            ->label('Slutt'),
                                                        Select::make('intensity')->options([
                                                            'green'    => 'Lett',
                                                            'darkcyan' => 'Vedlikehold',
                                                            'crimson'  => 'Tung',
                                                        ])->label('Hvor tung?'),
                                                    ])
                                                    ->defaultItems(0)
                                                    ->grid(4)
                                                    ->itemLabel(
                                                        fn(array $state): ?string => Exercise::all()->where('id', $state['exercise_id'])->first()?->name
                                                    )
                                                    ->addActionLabel('Legg til økt')
                                                    ->collapsible(),
                                            ]),
                                        Tabs\Tab::make('Søndag')
                                            ->badge(fn($record) => WeekplanExercise::where('day', 7)->where('weekplan_id', $record?->id)->count())
                                            ->schema([
                                                Repeater::make('exercises_7')
                                                    ->relationship('weekplanExercises', function ($query) {
                                                        $query->where('day', 7);
                                                    })
                                                    ->label('Økter')
                                                    ->schema([
                                                        Hidden::make('day')->default('7'),
                                                        Select::make('exercise_id')
                                                            ->options(Exercise::all()->pluck('name', 'id'))
                                                            ->label('Øvelse')
                                                            ->live(onBlur: true),
                                                        TimePicker::make('start_time')
                                                            ->seconds(false)
                                                            ->label('Start'),
                                                        TimePicker::make('end_time')
                                                            ->seconds(false)
                                                            ->label('Slutt'),
                                                        Select::make('intensity')->options([
                                                            'green'    => 'Lett',
                                                            'darkcyan' => 'Vedlikehold',
                                                            'crimson'  => 'Tung',
                                                        ])->label('Hvor tung?'),
                                                    ])
                                                    ->defaultItems(0)
                                                    ->grid(4)
                                                    ->itemLabel(
                                                        fn(array $state): ?string => Exercise::all()->where('id', $state['exercise_id'])->first()?->name
                                                    )
                                                    ->addActionLabel('Legg til økt')
                                                    ->collapsible(),
                                            ]),
                                    ]),
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
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make()
            ])
            ->actions([
                Actions\EditAction::make(),
                Actions\ViewAction::make(),
                Actions\DeleteAction::make()->label('Arkiver'),
                Actions\ForceDeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Actions\ForceDeleteBulkAction::make()
                ]),
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
