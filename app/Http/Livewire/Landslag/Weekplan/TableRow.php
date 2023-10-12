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

    public const DAYS_IN_WEEK    = 7;
    public const MINUTES_IN_HOUR = 60;

    public $data;
    public $exists = [];
    public $weekplanId;

    public function mount($weekplanId)
    {
        $this->weekplanId = $weekplanId;
        $this->data       = $this->getExercises();
    }

    /**
     * Retrieves the exercises based on the settings and time range.
     *
     * @return array The array of exercises.
     */
    public function getExercises(): array
    {
        // Retrieve settings and time range
        $setting   = $this->getSettings();
        $timeRange = $this->calculateTimeRange($setting['weekplan_timespan']);
        $startTime = intval($timeRange['startTime']);
        $endTime   = intval($timeRange['endTime']);
        $interval  = $timeRange['interval'];

        $groupedExercises = $this->groupExercisesByDay($this->weekplanId);

        return $this->generateTimeTableRows($startTime, $endTime, $interval, $groupedExercises);
    }

    /**
     * Retrieves the user settings.
     *
     * @return Settings The user settings.
     */
    private function getSettings(): Settings
    {
        return Settings::where('user_id', Auth::id())->first();
    }

    /**
     * Calculates the time range based on the given parameters.
     *
     * @param  int  $fixed  (optional) Whether to use the fixed time range. Defaults to 0.
     * @return array An array containing the start time, end time, and interval.
     * @throws Some_Exception_Class A description of the exception that can be thrown.
     */
    private function calculateTimeRange($fixed = 0): array
    {
        $exerciseData = $this->getDayData($this->weekplanId);
        $setting      = $this->getSettings();

        if ($fixed) {
            $startTime = Carbon::createFromFormat('H:i:s', $setting->weekplan_from)->format('H:i'); // Start time in hours (24-hour format)
            $endTime   = Carbon::createFromFormat('H:i:s', $setting->weekplan_to)->format('H:i'); // End time in hours (24-hour format)
        } else {
            $earliestTime = PHP_INT_MAX;
            $latestTime   = 0;

            // Find the earliest and latest exercise times
            foreach ($exerciseData as $item) {
                foreach ($item['exercises'] as $exercise) {
                    [$fromTime, $toTime] = array_map(function ($time) {
                        return date('H', strtotime($time)) * self::MINUTES_IN_HOUR + date('i', strtotime($time));
                    }, [$exercise['from'], $exercise['to']]);

                    $earliestTime = min($earliestTime, $fromTime);
                    $latestTime   = max($latestTime, $toTime);
                }
            }

            $startTime = floor($earliestTime / self::MINUTES_IN_HOUR); // Start time in hours (24-hour format)
            $endTime   = ceil($latestTime / self::MINUTES_IN_HOUR); // End time in hours (24-hour format)
        }

        $interval = self::MINUTES_IN_HOUR; // Interval in minutes

        return compact('startTime', 'endTime', 'interval');
    }

    /**
     * Retrieves the day data based on the given weekplan ID.
     *
     * @param  int  $weekplanId  The ID of the weekplan.
     * @return array The day data organized by day.
     */
    public function getDayData(int $weekplanId): array
    {
        // Fetch the related exercises from the WeekplanExercise model based on weekplan_id
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

        // Organize the data by day
        foreach ($days as $dayIndex => $dayName) {
            $exercisesForDay = $weekplanExercises->where('day', $dayIndex);
            $exerciseData    = [];

            foreach ($exercisesForDay as $exercise) {
                $exerciseData[] = [
                    'exercise'  => $exercise->exercise->name,
                    'from'      => $exercise->start_time,
                    'to'        => $exercise->end_time,
                    'intensity' => $exercise->intensity,
                ];
            }

            $data[] = [
                'day'       => $dayName,
                'exercises' => $exerciseData,
            ];
        }

        return $data;
    }

    /**
     * Groups the exercises of a weekplan by day.
     *
     * @param  int  $weekplanId  The ID of the weekplan.
     * @return array The grouped exercises.
     */
    private function groupExercisesByDay(int $weekplanId): array
    {
        $groupedExercises = [];

        foreach (Weekplan::with('weekplanExercises')->find($weekplanId)->weekplanExercises as $weekplanExercise) {
            $day = $weekplanExercise->day;
            if (!isset($groupedExercises[$day])) {
                $groupedExercises[$day] = [];
            }
            $groupedExercises[$day][] = $weekplanExercise;
        }

        return $groupedExercises;
    }

    /**
     * Generates time table rows based on the given start time, end time, interval, and grouped exercises.
     *
     * @param  int  $startTime  The start time in hours.
     * @param  int  $endTime  The end time in hours.
     * @param  int  $interval  The interval in minutes.
     * @param  array  $groupedExercises  An array of grouped exercises.
     * @return array The generated time table rows.
     */
    private function generateTimeTableRows(int $startTime, int $endTime, int $interval, array $groupedExercises): array
    {
        $data = [];

        for ($time = $startTime; $time <= $endTime; $time++) {
            for ($minute = 0; $minute < self::MINUTES_IN_HOUR; $minute += $interval) {
                $row = [
                    'time'      => sprintf('%02d:%02d', $time, $minute),
                    'exercises' => [],
                ];

                foreach ($groupedExercises as $day => $exercisesForDay) {
                    $filteredExercises = collect($exercisesForDay)->filter(function ($exercise) use ($time, $minute, $interval) {
                        $from          = strtotime($exercise->start_time);
                        $to            = strtotime($exercise->end_time);
                        $fromTime      = (date('H', $from) * self::MINUTES_IN_HOUR) + date('i', $from);
                        $toTime        = (date('H', $to) * self::MINUTES_IN_HOUR) + date('i', $to);
                        $intervalSlots = max(($toTime - $fromTime) / $interval, 1);
                        $currentTime   = ($time * self::MINUTES_IN_HOUR) + $minute;

                        return $currentTime >= $fromTime && $currentTime < ($fromTime + $intervalSlots * $interval);
                    });

                    $exerciseDataForDay = null;
                    if ($filteredExercises->isNotEmpty()) {
                        $exercise        = $filteredExercises->first();
                        $trainingProgram = $exercise->trainingProgram;

                        $exerciseDataForDay = [
                            'day'        => $day,
                            'time'       => formatTime(Carbon::parse($exercise->start_time)->format('H:i'),
                                Carbon::parse($exercise->end_time)->format('H:i')),
                            'intensity'  => $exercise->intensity,
                            'exercise'   => $exercise->exercise->name,
                            'program'    => $trainingProgram ? $trainingProgram->program_name : null,
                            'program_id' => $trainingProgram ? $trainingProgram->id : null,
                        ];
                    }

                    $row['exercises'][$day] = $exerciseDataForDay;
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

    /**
     * Renders the view for the given function.
     *
     * @return View
     */
    public function render(): View
    {
        return view('livewire.landslag.weekplan.table-row');
    }
}