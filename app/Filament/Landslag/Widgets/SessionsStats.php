<?php

namespace App\Filament\Landslag\Widgets;

use App\Models\Weekplan;
use App\Models\WeekplanExercise;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Exception;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SessionsStats extends BaseWidget
{
    protected static ?string $pollingInterval = null;

    public ?Weekplan $record = null;

    /**
     * Retrieves the statistics for the weekplans.
     *
     * @return array<int, Stat> The statistics for the weekplans.
     *
     * @throws Exception
     */
    protected function getStats(): array
    {
        // Fetch all weekplans along with their exercises
        $weekplans = Weekplan::with('weekplanExercises.exercise')
            ->when($this->record, function ($query) {
                return $query->where('id', $this->record->id);
            })
            ->when(! $this->record, function ($query) {
                return $query->active();
            })
            ->get();

        $statistics = [
            'okter' => 0,
            'timer' => 0,
            'intensities' => [
                'crimson' => 0,
                'darkcyan' => 0,
                'green' => 0,
            ],
        ];

        foreach ($weekplans as $weekplan) {
            /** @var WeekplanExercise $exercise */
            foreach ($weekplan->weekplanExercises as $exercise) {
                $statistics['okter']++;
                $statistics['timer'] += Carbon::parse($exercise->end_time)->diffInSeconds(Carbon::parse($exercise->start_time));

                $statistics['intensities'][$exercise->intensity]++;
            }
        }

        $nextSession = $this->getNextSession();

        $dayString = match ($nextSession['day']) {
            0 => '',
            1 => 'i morgen ',
            default => ucfirst(Carbon::now()->addDays($nextSession['day'])->locale('nb_NO')->isoFormat('dddd')).' ',
        };

        return [
            Stat::make('Antall økter', $statistics['okter']),
            Stat::make('Antall timer', CarbonInterval::seconds($statistics['timer'])->cascade()->forHumans()),
            Stat::make('Antall U, V, R økter',
                'U: '.$statistics['intensities']['crimson'].', V: '.$statistics['intensities']['darkcyan'].', R: '.$statistics['intensities']['green']),
            Stat::make('Neste økt: '.$dayString.$nextSession['time'], $nextSession['session']),
        ];
    }

    /**
     * Retrieves the next session based on the current date and weekplan.
     *
     * @return array{session?: string, time?: string, day: int} The next session information containing the session name, time range, and day offset.
     */
    private function getNextSession(): array
    {
        $now = Carbon::now();
        $nextSession = [];

        // Eager load exercises
        $allExercises = WeekplanExercise::with('exercise')
            ->orderBy('start_time')
            ->get();

        // Iterate through the days of the week starting with today
        for ($dayOffset = 0; $dayOffset < 7; $dayOffset++) {
            $day = $now->copy()->addDays($dayOffset)->dayOfWeekIso;
            $exercises = $allExercises->filter(function ($exercise) use ($day, $dayOffset, $now) {
                return $exercise->day == $day &&
                    ($dayOffset != 0 || $exercise->start_time > $now->format('H:i:s'));
            });

            // If any exercises are found, return the first one
            if ($exercises->count() > 0) {
                $exercise = $exercises->first();
                $nextSession = [
                    'session' => $exercise->exercise->name,
                    'time' => Carbon::parse($exercise->start_time)->format('H:i').'-'.Carbon::parse($exercise->end_time)->format('H:i'),
                    'day' => $dayOffset,
                ];
                break;
            }
        }

        return $nextSession;
    }
}
