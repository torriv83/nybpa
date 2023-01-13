<?php

namespace App\Filament\Widgets;

use Carbon\Carbon;
use App\Models\Timesheet;
use App\Models\User as Users;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Auth\User;
use Spatie\Permission\Models\Permission;
use Filament\Widgets\StatsOverviewWidget\Card;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class UserStats extends BaseWidget
{
    protected static ?string $pollingInterval = null;
    protected static ?int $sort = 1;
    protected array|string|int $columnSpan = 12;

    protected function getCards(): array
    {

        $tider = Timesheet::whereBetween(
                    'fra_dato', [
                        Carbon::parse('first day of January')
                        ->format('Y-m-d H:i:s'), Carbon::now()
                    ]
                )->where('unavailable', '!=', '1');

        // Timer i uka igjen
        $hoursLeftPerWeek = '';
        $ukerIgjen = Carbon::parse(now())->floatDiffInWeeks(now()->endOfYear());
        $hoursLeft = intdiv(21900-$tider->sum('totalt'), 60);
        $hoursPerWeekLeft = $hoursLeft/$ukerIgjen;
        $hoursLeftPerWeek = $this->minutesToTime($hoursPerWeekLeft*60);

        return [
            Card::make('Antall Assistenter', Users::permission('Assistent')->count()),
            Card::make('Timer brukt i år', $this->minutesToTime($tider->sum('totalt'))),
            Card::make('Timer igjen', $this->minutesToTime(21900-$tider->sum('totalt')))
                ->description($hoursLeftPerWeek . ' timer i uka igjen')
        ];
    }

    private function minutesToTime($minutes){
        $hours   = floor($minutes / 60);
        $minutes = ($minutes % 60);
        $format  = '%02d:%02d';

        $done = sprintf($format, $hours, $minutes);

        return $done;
    }
}