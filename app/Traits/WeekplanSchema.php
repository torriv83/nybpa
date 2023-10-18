<?php
/**
 * Created by Tor Rivera.
 * Date: 15.10.2023
 * Time: 21.04
 * Company: Rivera Consulting
 */

namespace App\Traits;

use App\Models\Exercise;
use App\Models\TrainingProgram;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TimePicker;

trait WeekplanSchema
{

    public static function getCommonFields($dayNumber): array
    {
        return [

            Repeater::make('exercises_' . $dayNumber)
                ->relationship('weekplanExercises', function ($query) use ($dayNumber) {
                    $query->where('day', $dayNumber);
                })
                ->label('Økter')
                ->schema([
                    Hidden::make('day')->default($dayNumber),
                    Select::make('exercise_id')
                        ->options(Exercise::all()->pluck('name', 'id'))
                        ->label('Øvelse')
                        ->live(onBlur: true)
                        ->required(),
                    Select::make('training_program_id')
                        ->label('Velg styrkeprogram')
                        ->options(TrainingProgram::all()->pluck('program_name', 'id'))
                        ->hidden(function ($get) {
                            $exerciseName = Exercise::find($get('exercise_id'))->name ?? null;
                            return $exerciseName !== 'Styrketrening';
                        }),
                    TimePicker::make('start_time')
                        ->seconds(false)
                        ->label('Start')
                        ->required(),
                    TimePicker::make('end_time')
                        ->seconds(false)
                        ->label('Slutt')
                        ->required(),
                    Select::make('intensity')->options([
                        'green'    => 'Lett',
                        'darkcyan' => 'Vedlikehold',
                        'crimson'  => 'Tung',
                    ])
                        ->label('Hvor tung?')
                        ->required(),
                ])
                ->defaultItems(0)
                ->grid(4)
                ->itemLabel(
                    fn(array $state): ?string => Exercise::all()->where('id',
                        $state['exercise_id'])->first()?->name
                )
                ->addActionLabel('Legg til økt')
                ->collapsible(),

        ];
    }

}
