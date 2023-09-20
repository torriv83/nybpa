<?php

namespace App\Filament\Landslag\Resources\WeekplanResource\Widgets;

use App\Models\Weekplan;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Model;

class StatsOverview extends BaseWidget
{
    protected static ?string $pollingInterval = null;

    public ?Model $record = null;

    public function mount($record)
    {
        $this->record = $record;
    }

    protected function getStats(): array
    {

        $weekplans = Weekplan::find($this->record);

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

        return [
            Stat::make('Antall økter', $statistics['okter']),
            Stat::make('Antall timer', CarbonInterval::seconds($statistics['timer'])->cascade()->forHumans()),
            Stat::make('Antall U, V, R økter',
                'U: ' . $statistics['intensities']['crimson'] . ', V: ' . $statistics['intensities']['darkcyan'] . ', R: ' . $statistics['intensities']['green']),
        ];
    }
}