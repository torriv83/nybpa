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
    /**
     * Generates a common fields array for a given day number.
     *
     * @param  int  $dayNumber  The day number.
     * @return array<int, \Filament\Forms\Components\Repeater> The common fields array.
     */
    public static function getCommonFields(int $dayNumber): array
    {
        return [
            // Create a Repeater field with the day number as part of the field name
            Repeater::make('exercises_'.$dayNumber)
                ->relationship('weekplanExercises', function ($query) use ($dayNumber) {
                    $query->where('day', $dayNumber);
                })
                ->label('Økter')
                ->schema([
                    // Create a Hidden field for the day number
                    Hidden::make('day')->default($dayNumber),

                    // Create a Select field for the exercise id
                    Select::make('exercise_id')
                        ->options(Exercise::pluck('name', 'id'))
                        ->label('Øvelse')
                        ->live(onBlur: true)
                        ->required(),

                    // Create a Select field for the training program id
                    Select::make('training_program_id')
                        ->label('Velg styrkeprogram')
                        ->options(TrainingProgram::pluck('program_name', 'id'))
                        ->hidden(function ($get) {
                            $exerciseName = Exercise::find($get('exercise_id'))->name ?? null;

                            return $exerciseName !== 'Styrketrening';
                        }),

                    // Create a TimePicker field for the start time
                    TimePicker::make('start_time')
                        ->seconds(false)
                        ->label('Start')
                        ->required(),

                    // Create a TimePicker field for the end time
                    TimePicker::make('end_time')
                        ->seconds(false)
                        ->label('Slutt')
                        ->required(),

                    // Create a Select field for the intensity
                    Select::make('intensity')->options([
                        'green' => 'Lett',
                        'darkcyan' => 'Vedlikehold',
                        'crimson' => 'Tung',
                    ])
                        ->label('Hvor tung?')
                        ->required(),
                ])
                ->defaultItems(0)
                ->grid(4)
                ->itemLabel(
                    fn (array $state): ?string => Exercise::where('id', $state['exercise_id'])->first()?->name
                )
                ->addActionLabel('Legg til økt')
                ->collapsible(),
        ];
    }
}
