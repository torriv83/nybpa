<?php

namespace App\Filament\Landslag\Widgets;

use App\Models\Weekplan;
use App\Models\WeekplanExercise;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Model;

class SessionsStats extends BaseWidget
{

    protected static ?string $pollingInterval = null;

    public ?Model $record = null;

    /**
     * Retrieves the statistics for the weekplans.
     *
     * @return array The statistics for the weekplans.
     * @throws \Exception
     */
    protected function getStats(): array
    {
        // Fetch all weekplans along with their exercises
        $weekplans = Weekplan::with('weekplanExercises.exercise')
            ->when($this->record, function ($query) {
                return $query->where('id', $this->record->id);
            })
            ->when(!$this->record, function ($query) {
                return $query->where('is_active', 1);
            })
            ->get();

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
     * @return array The next session information containing the session name, time range, and day offset.
     */
    private function getNextSession(): array
    {
        $now         = Carbon::now();
        $nextSession = [];

        // Iterate through the days of the week starting with today
        for ($dayOffset = 0; $dayOffset < 7; $dayOffset++) {
            $day       = $now->copy()->addDays($dayOffset)->dayOfWeekIso;
            $exercises = WeekplanExercise::query()
                ->where('weekplan_id', $this->record?->id)
                ->where('day', $day)
                ->with('exercise')
                ->orderBy('start_time')
                ->where(function ($query) use ($dayOffset, $now) {
                    if ($dayOffset == 0) {
                        $query->where('start_time', '>', $now->format('H:i:s'));
                    }
                })
                ->get();

            // If any exercises are found, return the first one
            if ($exercises->count() > 0) {
                $exercise    = $exercises->first();
                $nextSession = [
                    'session' => $exercise->exercise->name,
                    'time'    => Carbon::parse($exercise->start_time)->format('H:i').'-'.Carbon::parse($exercise->end_time)->format('H:i'),
                    'day'     => $dayOffset
                ];
                break;
            }
        }

        return $nextSession;
    }

}
