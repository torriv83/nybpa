<?php

namespace App\Filament\Resources\WeekplanResource\Widgets;

use App\Models\Weekplan;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;

class StatsOverview extends BaseWidget
{
    protected static ?string $pollingInterval = null;

    public $record;

    protected function getCards(): array
    {
        $test       = Weekplan::find($this->record);
        $statistics = [
            'okter'       => 0,
            'timer'       => 0,
            'intensities' => [
                'crimson'  => 0,
                'darkcyan' => 0,
                'other'    => 0,
            ],
        ];

        foreach ($test[0]['data'] as $t) {
            foreach ($t['exercises'] as $o) {
                $statistics['okter']++;
                $statistics['timer'] += Carbon::parse($o['to'])->diffInSeconds($o['from']);

                if ($o['intensity'] == 'crimson') {
                    $statistics['intensities']['crimson']++;
                } elseif ($o['intensity'] == 'darkcyan') {
                    $statistics['intensities']['darkcyan']++;
                } else {
                    $statistics['intensities']['other']++;
                }
            }
        }

        return [
            Card::make('Antall økter', $statistics['okter']),
            Card::make('Antall timer', date('H:i', mktime(0, 0, $statistics['timer']))),
            Card::make('Antall U, V, R økter',
                'U: ' . $statistics['intensities']['crimson'] . ', V: ' . $statistics['intensities']['darkcyan'] . ', R: ' . $statistics['intensities']['other']),
        ];
    }


}
