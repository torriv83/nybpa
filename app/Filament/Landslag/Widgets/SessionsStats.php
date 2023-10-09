<?php

namespace App\Filament\Landslag\Widgets;

use App\Models\Weekplan;
use App\Models\WeekplanExercise;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SessionsStats extends BaseWidget
{

    protected static ?string $pollingInterval = null;

    protected function getStats(): array
    {

        // Fetch all weekplans along with their exercises
        $weekplans = Weekplan::with('weekplanExercises.exercise')->get();

        $statistics = [
            'okter'       => 0,
            'timer'       => 0,
            'intensities' => [
                'crimson'  => 0,
                'darkcyan' => 0,
                'green'    => 0,
            ],
        ];

        foreach ($weekplans as $weekplan) {
            foreach ($weekplan->weekplanExercises as $exercise) {
                $statistics['okter']++;
                $statistics['timer'] += Carbon::parse($exercise->end_time)->diffInSeconds(Carbon::parse($exercise->start_time));

                $statistics['intensities'][$exercise->intensity]++;
            }
        }

        $nextSession = $this->getNextSession();

        if ($nextSession['day'] == 0) {
            $dayString = '';
        } elseif ($nextSession['day'] == 1) {
            $dayString = 'i morgen ';
        } else {
            $dayString = ucfirst(Carbon::now()->addDays($nextSession['day'])->locale('nb_NO')->isoFormat('dddd')) . ' ';
        }

        return [
            Stat::make('Antall økter', $statistics['okter']),
            Stat::make('Antall timer', CarbonInterval::seconds($statistics['timer'])->cascade()->forHumans()),
            Stat::make('Antall U, V, R økter',
                'U: ' . $statistics['intensities']['crimson'] . ', V: ' . $statistics['intensities']['darkcyan'] . ', R: ' . $statistics['intensities']['green']),
            Stat::make('Neste økt: ' . $dayString . $nextSession['time'], $nextSession['session']),
        ];

    }

    //TODO refactor this

    private function getNextSession(){
        $now = Carbon::now();
        $nextSession = [];

        // Iterate through the days of the week starting with today
        for ($dayOffset = 0; $dayOffset < 7; $dayOffset++) {
            $day = $now->copy()->addDays($dayOffset)->dayOfWeek;
            $exercises = WeekplanExercise::where('day', $day)
                ->with('exercise')
                ->orderBy('start_time')
                ->get();

            // If today, filter exercises by start time being later than now
            if ($dayOffset == 0) {
                $exercises = $exercises->filter(function($exercise) use ($now) {
                    return $exercise->start_time > $now->format('H:i');
                });
            }

            // If any exercises are found, return the first one
            if ($exercises->count() > 0) {
                $exercise = $exercises->first();
                $nextSession['session'] = "{$exercise->exercise->name}";
                $nextSession['time'] = Carbon::parse($exercise->start_time)->format('H:i') . '-' . Carbon::parse($exercise->end_time)->format('H:i');
                $nextSession['day'] = $dayOffset;
                break;
            }
        }

        return $nextSession;
    }

}
