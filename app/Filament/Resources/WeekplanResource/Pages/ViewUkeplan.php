<?php

namespace App\Filament\Resources\WeekplanResource\Pages;

use App\Filament\Resources\WeekplanResource;
use App\Models\Weekplan;
use Carbon\Carbon;
use Filament\Resources\Pages\Page;

class ViewUkeplan extends Page
{
    protected static string  $resource = WeekplanResource::class;
    protected static string  $view     = 'filament.resources.weekplan-resource.pages.view-ukeplan';
    protected static ?string $title    = 'Ukeplan';

    public $record;

    public function mount($record): void
    {
        $this->record = Weekplan::find($record);
    }

    public function getDayData(): \Illuminate\Support\Collection
    {
        return collect($this->record['data']);
    }

    protected function getViewData(): array
    {

        $okter = $this->getDayData();
        $data  = $this->getExercises();

        return compact('okter', 'data');
    }

    public function getExercises(): array
    {

        $startTime = 9; // Start time in hours (24-hour format)
        $endTime   = 21; // End time in hours (24-hour format)
        $interval  = 60; // Interval in minutes

        $data = [];

        for ($time = $startTime; $time <= $endTime; $time++) {
            for ($minute = 0; $minute < 60; $minute += $interval) {
                $row = [
                    'time'      => sprintf('%02d', $time) . ':' . sprintf('%02d', $minute),
                    'exercises' => [],
                ];

                $i = 0;
                foreach ($this->getDayData() as $index => $item) {
                    $exercises = collect($item['exercises'])->filter(function ($exercise) use ($time, $minute, $interval) {
                        $from = strtotime($exercise['from']);
                        $to   = strtotime($exercise['to']);

                        $fromHour   = date('H', $from);
                        $fromMinute = date('i', $from);
                        $toHour     = date('H', $to);
                        $toMinute   = date('i', $to);

                        // Calculate the number of time slots based on the from and to times
                        $fromTime      = ($fromHour * 60) + $fromMinute;
                        $toTime        = ($toHour * 60) + $toMinute;
                        $intervalSlots = max(($toTime - $fromTime) / $interval, 1); // Ensure minimum 1 slot
                        $currentTime   = ($time * 60) + $minute;

                        return $currentTime >= $fromTime && $currentTime < ($fromTime + $intervalSlots * $interval);
                    });


                    if ($exercises->isNotEmpty()) {
                        foreach ($exercises as $exercise) {
                            $row['exercises'][] = [
                                'day'       => $item['day'],
                                'id'        => $i,
                                'time'      => formatTime($exercise['from'], $exercise['to']),
                                'intensity' => $exercise['intensity'],
                                'exercise'  => $exercise['exercise'],
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

    protected function getTitle(): string
    {
        // Retrieve the dynamic title from a database, configuration, or any other source
        return $this->record['name'];
    }

    protected function getSubheading(): string
    {
        $dynamicSubheading = $this->record['updated_at'];
        $formattedDate     = '';

        if ($dynamicSubheading) {
            $formattedDate = Carbon::parse($dynamicSubheading)->format('d.m.Y H:i');
        }

        return 'Sist oppdatert: ' . $formattedDate;
    }

    protected function getHeaderWidgets(): array
    {
        return [
            WeekplanResource\Widgets\StatsOverview::class,
        ];
    }

}
