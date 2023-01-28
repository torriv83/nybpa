<?php

namespace App\Filament\Widgets;

use DateTime;
use Carbon\Carbon;
use App\Models\Utstyr;
use App\Models\Timesheet;
use App\Settings\BpaTimer;
use App\Models\User as Users;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Auth\User;
use Spatie\Permission\Models\Permission;
use Filament\Widgets\StatsOverviewWidget\Card;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class UserStats extends BaseWidget
{
    protected static ?string $pollingInterval = '600s';
    protected static ?int $sort = 1;
    protected array|string|int $columnSpan = 12;

    protected function getColumns(): int
    {
        return 4;
    }

    protected function getCards(): array
    {

        $tider = Timesheet::whereBetween(
            'fra_dato',
            [
                Carbon::parse('first day of January')
                    ->format('Y-m-d H:i:s'), Carbon::now()
            ]
        )->where('unavailable', '!=', '1');

        /* Timer i uka igjen */
        $hoursLeftPerWeek = '';
        $ukerIgjen        = Carbon::parse(now())->floatDiffInWeeks(now()->endOfYear());
        $totalt_timer_innvilget = (app(BpaTimer::class)->timer * 52) * 60;
        // $hoursLeft        = intdiv(21900 - $tider->sum('totalt'), 60);
        // $hoursPerWeekLeft = $hoursLeft / $ukerIgjen;
        // $hoursLeftPerWeek = $this->minutesToTime($hoursPerWeekLeft * 60);

        /* Test */
        $total_minutes = $totalt_timer_innvilget; // convert total hours to minutes
        $hours_used_minutes = $tider->sum('totalt'); // convert hours used to minutes
        $hours_per_week = 24 * 7;
        $hours_left_per_week = (($total_minutes - $hours_used_minutes) / 60 - ($hours_per_week * $ukerIgjen)) / $ukerIgjen;

        $hoursLeftPerWeek = date("H:i", mktime(0, $hours_left_per_week * 60));

        /* Chart timer brukt dette året */
        $thisYear = Timesheet::whereBetween('fra_dato', [Carbon::parse('first day of January')->format('Y-m-d H:i:s'), Carbon::now()->endOfYear()])
            ->where('unavailable', '!=', 1)
            ->orderByRaw('fra_dato ASC')
            ->get()
            ->groupBy(function ($val) {

                return Carbon::parse($val->fra_dato)->isoFormat('MMM');
            });

        $thisYearTimes = [];
        $sum = 0;
        foreach ($thisYear as $key => $value) {

            for ($i = 0; $i < count($value); $i++) {
                $sum += $value[$i]->totalt;
            }

            $thisYearTimes[$key] = round($sum / 21900 * 100, 1);
        }

        /* Card Widgets */
        return [
            Card::make('Antall Assistenter', Users::assistenter()->count()),
            Card::make('Timer brukt i år', $this->minutesToTime($tider->sum('totalt')))
                ->chart($thisYearTimes)
                ->color('success'),
            Card::make('Timer igjen', $this->minutesToTime(21900 - $tider->sum('totalt')))
                ->description('I gjennomsnitt ' . $hoursLeftPerWeek . ' i uka igjen'),
            Card::make('Antall utstyr på lista', Utstyr::all()->count()),
        ];
    }

    private function minutesToTime($minutes)
    {
        $hours   = $minutes / 60;
        $minutes = ($minutes % 60);
        $format  = '%02d:%02d';

        $done = sprintf($format, $hours, $minutes);

        return $done;
    }
}
