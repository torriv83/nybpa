<?php

namespace App\Http\Livewire\Landslag\Weekplan;

use App\Models\Settings;
use App\Models\Weekplan;
use App\Models\WeekplanExercise;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class TableRow extends Component
{
    public const DAYS_IN_WEEK = 7;

    public const MINUTES_IN_HOUR = 60;

    /** @var array<int, array{time: string, exercises: array<int|string, array<string, mixed>>}> */
    public array $data;

    /** @var array<int|string, mixed> */
    public array $exists = [];

    public int|string|null $weekplanId;

    public function mount(): void
    {
        $this->data = $this->getExercises();
    }

    public function render(): View
    {
        return view('livewire.landslag.weekplan.table-row');
    }

    /**
     * @return array<int, array{time: string, exercises: array<int|string, array<string, mixed>>}>
     */
    public function getExercises(): array
    {
        $setting = $this->getSettings();
        $timeRange = $this->calculateTimeRange($setting['weekplan_timespan']);
        $startTime = intval($timeRange['startTime']);
        $endTime = intval($timeRange['endTime']);
        $interval = $timeRange['interval'];

        $groupedExercises = $this->groupExercisesByDay($this->weekplanId);

        return $this->generateTimeTableRows($startTime, $endTime, $interval, $groupedExercises);
    }

    private function getSettings(): Settings
    {
        return Settings::where('user_id', Auth::id())->first();
    }

    /**
     * @return array{startTime: int|float, endTime: int|float, interval: int}
     */
    private function calculateTimeRange(int $fixed = 0): array
    {
        $exerciseData = $this->getDayData($this->weekplanId);
        $setting = $this->getSettings();

        if ($fixed) {
            $startTime = (int) Carbon::createFromFormat('H:i:s', $setting->weekplan_from)->format('H');
            $endTime = (int) Carbon::createFromFormat('H:i:s', $setting->weekplan_to)->format('H');
        } else {
            $earliestTime = PHP_INT_MAX;
            $latestTime = 0;

            foreach ($exerciseData as $item) {
                foreach ($item['exercises'] as $exercise) {
                    [$fromTime, $toTime] = array_map(function ($time): int {
                        return (int) date('H', strtotime($time)) * self::MINUTES_IN_HOUR + (int) date('i', strtotime($time));
                    }, [$exercise['from'], $exercise['to']]);

                    $earliestTime = min($earliestTime, $fromTime);
                    $latestTime = max($latestTime, $toTime);
                }
            }

            $startTime = floor($earliestTime / self::MINUTES_IN_HOUR);
            $endTime = ceil($latestTime / self::MINUTES_IN_HOUR);
        }

        $interval = self::MINUTES_IN_HOUR;

        return compact('startTime', 'endTime', 'interval');
    }

    /**
     * @phpstan-return array<int, array{day: string, exercises: list<array{exercise: string, from: string, to: string, intensity: string|int}>}>
     */
    public function getDayData(?int $weekplanId): array
    {
        $weekplanExercises = WeekplanExercise::where('weekplan_id', $weekplanId)
            ->with(['exercise'])
            ->orderBy('day')
            ->get();

        $days = [
            1 => 'Mandag',
            2 => 'Tirsdag',
            3 => 'Onsdag',
            4 => 'Torsdag',
            5 => 'Fredag',
            6 => 'Lørdag',
            7 => 'Søndag',
        ];

        $data = [];

        foreach ($days as $dayIndex => $dayName) {
            $exercisesForDay = $weekplanExercises->where('day', $dayIndex);
            $exerciseData = [];

            foreach ($exercisesForDay as $exercise) {
                $exerciseData[] = [
                    'exercise' => $exercise->exercise->name,
                    'from' => $exercise->start_time,
                    'to' => $exercise->end_time,
                    'intensity' => $exercise->intensity,
                ];
            }

            $data[] = [
                'day' => $dayName,
                'exercises' => $exerciseData,
            ];
        }

        return $data;
    }

    /**
     * @phpstan-return array<int, list<\App\Models\WeekplanExercise>>
     */
    private function groupExercisesByDay(?int $weekplanId): array
    {
        $groupedExercises = [];

        $weekplan = Weekplan::with('weekplanExercises')->find($weekplanId);

        if ($weekplan === null) {
            return $groupedExercises;
        }

        /** @var list<\App\Models\WeekplanExercise> $exercises */
        $exercises = $weekplan->weekplanExercises;

        foreach ($exercises as $weekplanExercise) {
            $day = $weekplanExercise->day;
            $groupedExercises[$day][] = $weekplanExercise;
        }

        return $groupedExercises;
    }

    /**
     * @param  array<int, array<\App\Models\WeekplanExercise>>  $groupedExercises
     * @return array<int, array{time: string, exercises: array<int|string, array<string, mixed>>}>
     */
    private function generateTimeTableRows(int $startTime, int $endTime, int $interval, array $groupedExercises): array
    {
        $data = [];

        for ($time = $startTime; $time <= $endTime; $time++) {
            for ($minute = 0; $minute < self::MINUTES_IN_HOUR; $minute += $interval) {
                $row = [
                    'time' => sprintf('%02d:%02d', $time, $minute),
                    'exercises' => [],
                ];

                foreach ($groupedExercises as $day => $exercisesForDay) {
                    $filteredExercises = collect($exercisesForDay)->filter(function ($exercise) use ($time, $minute, $interval): bool {
                        $from = strtotime($exercise->start_time);
                        $to = strtotime($exercise->end_time);
                        $fromTime = ((int) date('H', $from) * self::MINUTES_IN_HOUR) + (int) date('i', $from);
                        $toTime = ((int) date('H', $to) * self::MINUTES_IN_HOUR) + (int) date('i', $to);
                        $intervalSlots = max(($toTime - $fromTime) / $interval, 1);
                        $currentTime = ($time * self::MINUTES_IN_HOUR) + $minute;

                        return $currentTime >= $fromTime && $currentTime < ($fromTime + $intervalSlots * $interval);
                    });

                    $exerciseDataForDay = null;
                    if ($filteredExercises->isNotEmpty()) {
                        $exercise = $filteredExercises->first();
                        $trainingProgram = $exercise->trainingProgram;

                        $fromTime = ((int) date('H', strtotime($exercise->start_time)) * self::MINUTES_IN_HOUR) + (int) date('i', strtotime($exercise->start_time));
                        $toTime = ((int) date('H', strtotime($exercise->end_time)) * self::MINUTES_IN_HOUR) + (int) date('i', strtotime($exercise->end_time));
                        $rowspan = ceil(($toTime - $fromTime) / $interval);

                        $exerciseDataForDay = [
                            'day' => $day,
                            'time' => Carbon::parse($exercise->start_time)->format('H:i').' - '.Carbon::parse($exercise->end_time)->format('H:i'),
                            'intensity' => $exercise->intensity,
                            'exercise' => $exercise->exercise->name,
                            'program' => $trainingProgram?->program_name,
                            'program_id' => $trainingProgram?->id,
                            'rowspan' => $rowspan,
                        ];
                    }

                    $row['exercises'][$day] = $exerciseDataForDay ?? [];
                }

                for ($day = 1; $day <= self::DAYS_IN_WEEK; $day++) {
                    $row['exercises'][$day] = $row['exercises'][$day] ?? [];
                }

                ksort($row['exercises']);

                $data[] = $row;
            }
        }

        return $data;
    }
}
