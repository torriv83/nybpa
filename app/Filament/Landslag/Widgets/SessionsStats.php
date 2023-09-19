<?php

namespace App\Filament\Landslag\Widgets;

use App\Models\Weekplan;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SessionsStats extends BaseWidget
{

    protected static ?string $pollingInterval = null;

    protected function getStats(): array
    {

        $weekplans = Weekplan::all();

        if (!$weekplans) {
            // Handle the situation when the record is not found
            return [];
        }

        $statistics = [
            'okter'       => 0,
            'timer'       => 0,
            'intensities' => [
                'crimson'  => 0,
                'darkcyan' => 0,
                'green'    => 0,
            ],
        ];

        foreach ($weekplans[0]['data'] as $weekplan) {
            foreach ($weekplan['exercises'] as $exercise) {
                $statistics['okter']++;
                $statistics['timer'] += Carbon::parse($exercise['to'])->diffInSeconds($exercise['from']);

                $statistics['intensities'][$exercise['intensity']]++;

            }
        }

        $todayExercises = [];
        $today = ucfirst(\Carbon\Carbon::now()->translatedFormat('l'));

        foreach ($weekplans as $weekplan) {
            foreach ($weekplan->data as $dayPlan) {
                if ($dayPlan['day'] === $today) {
                    $todayExercises[] = $dayPlan['exercises'];
                }
            }
        }

        // Flattening the array if multiple plans exist for the same day
        $todayExercises = array_merge([], ...$todayExercises);

        return [
            Stat::make('Antall økter', $statistics['okter']),
            Stat::make('Antall timer', CarbonInterval::seconds($statistics['timer'])->cascade()->forHumans()),
            Stat::make('Antall U, V, R økter',
                'U: ' . $statistics['intensities']['crimson'] . ', V: ' . $statistics['intensities']['darkcyan'] . ', R: ' . $statistics['intensities']['green']),
            Stat::make('Neste økt', function () use ($todayExercises)
            {
                $now = \Carbon\Carbon::now()->format('H:i');
                $nextSession = null;

                // Sorter øktene etter tidspunktet "fra"
                usort($todayExercises, function ($a, $b) {
                    return $a['from'] <=> $b['from'];
                });

                // Finn neste økt basert på nåværende tid
                foreach ($todayExercises as $exercise) {
                    if ($exercise['from'] > $now) {
                        $nextSession = "{$exercise['from']} - {$exercise['to']}: {$exercise['exercise']}";
                        break;
                    }
                }

                return $nextSession ?? 'Ingen flere økter i dag';
            }),
        ];
    }
}
