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

        $today = Carbon::now()->dayOfWeek + 1; // Convert Sunday to 7, Monday to 1, etc.

        $todayExercises = WeekplanExercise::where('day', $today)
            ->with(['exercise'])
            ->get();

        return [
            Stat::make('Antall økter', $statistics['okter']),
            Stat::make('Antall timer', CarbonInterval::seconds($statistics['timer'])->cascade()->forHumans()),
            Stat::make('Antall U, V, R økter',
                'U: ' . $statistics['intensities']['crimson'] . ', V: ' . $statistics['intensities']['darkcyan'] . ', R: ' . $statistics['intensities']['green']),
            Stat::make('Neste økt', function () use ($todayExercises)
            {
                $now = Carbon::now()->format('H:i');
                $nextSession = null;

                // Sort the exercises by the start_time
                $todayExercises = $todayExercises->sortBy('start_time');

                // Find the next session based on the current time
                foreach ($todayExercises as $exercise) {
                    if ($exercise->start_time > $now) {
                        $nextSession = "{$exercise->start_time} - {$exercise->end_time}: {$exercise->exercise->name}";
                        break;
                    }
                }

                return $nextSession ?? 'Ingen flere økter i dag';
            }),
        ];

    }
}
