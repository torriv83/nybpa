<?php

namespace App\Filament\Resources\WeekplanResource\Widgets;

use App\Models\Weekplan;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;

class StatsOverview extends BaseWidget
{
    // protected static string $view = 'filament.resources.economy-resource.widgets.stats-overview';
    protected static ?string $pollingInterval = null;

    public $record;

    protected function getCards(): array
    {

        $test  = Weekplan::find($this->record);
        $okter = 0;
        $timer = 0;

        foreach ($test[0]['data'] as $t) {
            foreach ($t['exercises'] as $o) {
                $okter++;
                $timer += Carbon::parse($o['to'])->diffInSeconds($o['from']);

            }
        }

        return [
            Card::make('Antall økter', $okter),
            Card::make('Antall timer', date('H:i', mktime(0, 0, $timer))),
        ];
    }

}
