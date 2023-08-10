<?php

namespace App\Filament\Resources\WeekplanResource\Pages;

use App\Filament\Resources\WeekplanResource;
use App\Models\Settings;
use App\Models\Weekplan;
use Carbon\Carbon;
use Filament\Pages\Actions;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class ViewUkeplan extends Page
{
    protected static string $resource = WeekplanResource::class;

    protected static string $view = 'filament.resources.weekplan-resource.pages.view-ukeplan';

    protected static ?string $title = 'Ukeplan';

    public $record;

    public function mount($record): void
    {
        $this->record = Weekplan::find($record);
    }

    protected function getActions(): array
    {
        return [
            Actions\EditAction::make()->url('./'.$this->record->id.'/edit'),
        ];
    }

    protected function getViewData(): array
    {

        $okter = $this->getDayData();
        $data = $this->getExercises();

        return compact('okter', 'data');
    }

    public function getDayData(): Collection
    {
        return collect($this->record['data']);
    }

    public function getExercises(): array
    {

        $setting = Settings::where('user_id', '=', Auth::id())->first();

        // Usage of the extracted function
        $timeRange = $this->calculateTimeRange($setting['weekplan_timespan']);

        $startTime = intval($timeRange['startTime']);
        $endTime = intval($timeRange['endTime']);
        $interval = $timeRange['interval'];

        $data = [];

        for ($time = $startTime; $time <= $endTime; $time++) {
            for ($minute = 0; $minute < 60; $minute += $interval) {
                $row = [
                    'time' => sprintf('%02d', $time).':'.sprintf('%02d', $minute),
                    'exercises' => [],
                ];

                $i = 0;
                foreach ($this->getDayData() as $item) {
                    $exercises = collect($item['exercises'])->filter(function ($exercise) use ($time, $minute, $interval) {
                        $from = strtotime($exercise['from']);
                        $to = strtotime($exercise['to']);

                        $fromHour = date('H', $from);
                        $fromMinute = date('i', $from);
                        $toHour = date('H', $to);
                        $toMinute = date('i', $to);

                        // Calculate the number of time slots based on the from and to times
                        $fromTime = ($fromHour * 60) + $fromMinute;
                        $toTime = ($toHour * 60) + $toMinute;
                        $intervalSlots = max(($toTime - $fromTime) / $interval, 1); // Ensure minimum 1 slot
                        $currentTime = ($time * 60) + $minute;

                        return $currentTime >= $fromTime && $currentTime < ($fromTime + $intervalSlots * $interval);
                    });

                    if ($exercises->isNotEmpty()) {
                        foreach ($exercises as $exercise) {
                            $row['exercises'][] = [
                                'day' => $item['day'],
                                'id' => $i,
                                'time' => formatTime($exercise['from'], $exercise['to']),
                                'intensity' => $exercise['intensity'],
                                'exercise' => $exercise['exercise'],
                            ];
                        }
                    } else {
                        $row['exercises'][] = [];
                    }
                    $i++;
                }

                $data[] = $row;
            }
        }

        return $data;
    }

    private function calculateTimeRange($fixed = 0): array
    {
        $exerciseData = $this->getDayData();
        $setting = Settings::where('user_id', '=', \Auth::id())->first();

        if ($fixed) {
            $startTime = Carbon::createFromFormat('H:i:s', $setting->weekplan_from)->format('H:i'); // Start time in hours (24-hour format)
            $endTime = Carbon::createFromFormat('H:i:s', $setting->weekplan_to)->format('H:i'); // End time in hours (24-hour format)

        } else {
            $earliestTime = PHP_INT_MAX;
            $latestTime = 0;

            // Find the earliest and latest exercise times
            foreach ($exerciseData as $item) {
                foreach ($item['exercises'] as $exercise) {
                    $from = strtotime($exercise['from']);
                    $to = strtotime($exercise['to']);

                    $fromTime = date('H', $from) * 60 + date('i', $from);
                    $toTime = date('H', $to) * 60 + date('i', $to);

                    $earliestTime = min($earliestTime, $fromTime);
                    $latestTime = max($latestTime, $toTime);
                }
            }

            $startTime = floor($earliestTime / 60); // Start time in hours (24-hour format)
            $endTime = ceil($latestTime / 60); // End time in hours (24-hour format)
        }

        $interval = 60; // Interval in minutes

        return [
            'startTime' => $startTime,
            'endTime' => $endTime,
            'interval' => $interval,
        ];
    }

    protected function getTitle(): string
    {
        // Retrieve the dynamic title from a database, configuration, or any other source
        return $this->record['name'];
    }

    protected function getSubheading(): string
    {
        $dynamicSubheading = $this->record['updated_at'];
        $formattedDate = '';

        if ($dynamicSubheading) {
            $formattedDate = Carbon::parse($dynamicSubheading)->diffForHumans();
        }

        return 'Sist oppdatert: '.$formattedDate;
    }

    protected function getHeaderWidgets(): array
    {
        return [
            WeekplanResource\Widgets\StatsOverview::class,
        ];
    }
}
