<?php

namespace App\Filament\Landslag\Resources\WeekplanResource\Pages;

use App\Filament\Landslag\Resources\WeekplanResource;
use App\Filament\Landslag\Widgets\SessionsStats;
use App\Models\Settings;
use App\Models\Weekplan;
use App\Models\WeekplanExercise;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\Auth;

class ViewWeekplan extends Page
{
    protected static string $resource = WeekplanResource::class;

    protected static string $view = 'filament.resources.weekplan-resource.pages.view-ukeplan';

    public array $exercises;

    public function getTitle(): string
    {
        // Retrieve the dynamic title from a database, configuration, or any other source
        return $this->record['name'];
    }

    public function getSubheading(): string
    {
        $dynamicSubheading = $this->record['updated_at'];
        $formattedDate     = '';

        if ($dynamicSubheading) {
            $formattedDate = Carbon::parse($dynamicSubheading)->diffForHumans();
        }

        return 'Sist oppdatert: ' . $formattedDate;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('edit')->url('./' . $this->record->id . '/edit'),
        ];
    }

    public function mount($record): void
    {
        $this->record = Weekplan::with('weekplanExercises')->find($record);
        $this->exercises = $this->getExercises();
    }

    protected function getViewData(): array
    {

        $okter = $this->getDayData($this->record->id);
        $data  = $this->getExercises();

        return compact('okter', 'data');
    }


    public function getDayData($weekplanId): array
    {
        // Fetch the related exercises from the WeekplanExercise model based on weekplan_id
        $weekplanExercises = WeekplanExercise::where('weekplan_id', $weekplanId)
            ->with(['exercise'])
            ->orderBy('day', 'asc')
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
            $exerciseData = [];

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
     * Retrieves an array of exercises based on the current user's settings.
     *
     * @return array An array of exercises with their corresponding time slots and intensities.
     */
    public function getExercises(): array
    {
        // Retrieve settings and time range
        $setting = Settings::where('user_id', '=', Auth::id())->first();
        $timeRange = $this->calculateTimeRange($setting['weekplan_timespan']);
        $startTime = intval($timeRange['startTime']);
        $endTime = intval($timeRange['endTime']);
        $interval = $timeRange['interval'];

        // Initialize data and groupedExercises arrays
        $data = [];
        $groupedExercises = [];

        // Group exercises by day
        foreach ($this->record->weekplanExercises as $weekplanExercise) {
            $day = $weekplanExercise->day;
            if (!isset($groupedExercises[$day])) {
                $groupedExercises[$day] = [];
            }
            $groupedExercises[$day][] = $weekplanExercise;
        }

        // Generate timetable rows
        for ($time = $startTime; $time <= $endTime; $time++) {
            for ($minute = 0; $minute < 60; $minute += $interval) {
                $row = [
                    'time' => sprintf('%02d', $time) . ':' . sprintf('%02d', $minute),
                    'exercises' => [],
                ];

                // Loop through grouped exercises by day
                foreach ($groupedExercises as $day => $exercisesForDay) {
                    $filteredExercises = collect($exercisesForDay)->filter(function ($exercise) use ($time, $minute, $interval) {

                        $from = strtotime($exercise->start_time);
                        $to = strtotime($exercise->end_time);
                        $fromTime = (date('H', $from) * 60) + date('i', $from);
                        $toTime = (date('H', $to) * 60) + date('i', $to);
                        $intervalSlots = max(($toTime - $fromTime) / $interval, 1);
                        $currentTime = ($time * 60) + $minute;

                        return $currentTime >= $fromTime && $currentTime < ($fromTime + $intervalSlots * $interval);
                    });

                    // Add filtered exercises to row
                    $exerciseDataForDay = [];

                    if ($filteredExercises->isNotEmpty()) {
                        foreach ($filteredExercises as $exercise) {
                            $trainingProgram = $exercise->trainingProgram;
                            $exerciseDataForDay = [
                                'day' => $day,
                                'time' => formatTime(Carbon::parse($exercise->start_time)->format('H:i'), Carbon::parse($exercise->end_time)->format('H:i')),
                                'intensity' => $exercise->intensity,
                                'exercise' => $exercise->exercise->name,
                                'program' => $trainingProgram ? $trainingProgram->program_name : null,
                                'program_id' => $trainingProgram ? $trainingProgram->id : null,
                            ];
                        }
                    }

                    $row['exercises'][$day] = $exerciseDataForDay;
                }

                for ($day = 1; $day <= 7; $day++) {
                    if (!isset($row['exercises'][$day])) {
                        $row['exercises'][$day] = [];
                    }
                }

                // Sort exercises by day to keep them in order
                ksort($row['exercises']);

                // Add row to data
                $data[] = $row;
            }
        }

        return $data;
    }

    /**
     * Calculates the time range based on the exercise data and user settings.
     *
     * @param int $fixed Indicates if the time range should be fixed or dynamic. Default is 0.
     * @throws Some_Exception_Class Exception thrown if there is an error retrieving the user settings.
     * @return array An array containing the start time, end time, and interval.
     */
    private function calculateTimeRange($fixed = 0): array
    {
        $exerciseData = $this->getDayData($this->record->id);
        $setting      = Settings::where('user_id', '=', Auth::id())->first();

        if ($fixed) {
            $startTime = Carbon::createFromFormat('H:i:s', $setting->weekplan_from)->format('H:i'); // Start time in hours (24-hour format)
            $endTime   = Carbon::createFromFormat('H:i:s', $setting->weekplan_to)->format('H:i'); // End time in hours (24-hour format)

        } else {
            $earliestTime = PHP_INT_MAX;
            $latestTime   = 0;

            // Find the earliest and latest exercise times
            foreach ($exerciseData as $item) {
                foreach ($item['exercises'] as $exercise) {
                    $from = strtotime($exercise['from']);
                    $to   = strtotime($exercise['to']);

                    $fromTime = date('H', $from) * 60 + date('i', $from);
                    $toTime   = date('H', $to) * 60 + date('i', $to);

                    $earliestTime = min($earliestTime, $fromTime);
                    $latestTime   = max($latestTime, $toTime);
                }
            }

            $startTime = floor($earliestTime / 60); // Start time in hours (24-hour format)
            $endTime   = ceil($latestTime / 60); // End time in hours (24-hour format)
        }

        $interval = 60; // Interval in minutes

        return [
            'startTime' => $startTime,
            'endTime'   => $endTime,
            'interval'  => $interval,
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            SessionsStats::make(['record' => $this->record]),
        ];
    }
}
