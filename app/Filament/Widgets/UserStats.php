<?php

namespace App\Filament\Widgets;

//use DateTime;
use Carbon\Carbon;
use App\Models\Utstyr;
use App\Models\Timesheet;
use App\Settings\BpaTimer;
use App\Models\User as Users;
//use Spatie\Permission\Models\Role;
//use Illuminate\Foundation\Auth\User;
//use Spatie\Permission\Models\Permission;
use Filament\Widgets\StatsOverviewWidget\Card;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class UserStats extends BaseWidget
{
    protected static ?string $pollingInterval = '600s';
    protected static ?int $sort = 1;

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
        $ukerIgjen              = Carbon::parse(now())->floatDiffInWeeks(now()->endOfYear());
        $totaltTimerInnvilget = (app(BpaTimer::class)->timer * 52) * 60;

        // Kalkulasjoner
        $totalMinutes       = $totaltTimerInnvilget; // convert total hours to minutes
        $hoursUsedMinutes  = $tider->sum('totalt'); // convert hours used to minutes
        $hoursPerWeek      = 24 * 7;
        $leftPerWeek = (($totalMinutes - $hoursUsedMinutes) / 60 - ($hoursPerWeek * $ukerIgjen)) / $ukerIgjen;

        $avgPerWeek = date('H:i:s', mktime(0, $leftPerWeek * 60));

        /* Chart timer brukt dette året */
        $thisYear = Timesheet::whereBetween('fra_dato', [Carbon::parse('first day of January')->format('Y-m-d H:i:s'), Carbon::now()->endOfYear()])
            ->where('unavailable', '!=', 1)
            ->orderByRaw('fra_dato ASC')
            ->get()
            ->groupBy(fn($val) => Carbon::parse($val->fra_dato)->isoFormat('MMM'));

        $thisYearTimes = [];
        $sum = 0;
        foreach ($thisYear as $key => $value) {

            $number = count($value);
            for ($i = 0; $i < $number; $i++) {
                $sum += $value[$i]->totalt;
            }

            $thisYearTimes[$key] = round($sum / app(BpaTimer::class)->timer * 100, 1);
        }

        /**
         * Timer brukt denne mnd av totalt denne mnd
         */

        $hoursToUseThisMonth = (app(BpaTimer::class)->timer / 7) * Carbon::now()->daysInMonth;
        $usedThisMonth = Timesheet::thisMonth()->thisYear()->where('unavailable', '=', '0')->sum('totalt');

        /* Card Widgets */
        return [
            Card::make('Antall Assistenter', Users::assistenter()->count())
                ->url(route('filament.resources.users.index')),

            Card::make('Timer brukt i år', $this->minutesToTime($tider->sum('totalt')))
                ->chart($thisYearTimes)
                ->color('success')
                ->url(route('filament.resources.timesheets.index', 'tableFilters[måned][fra_dato]=2023-01-01&tableFilters[måned][til_dato]=2023-12-31'))
                ->description($this->minutesToTime($usedThisMonth) . ' brukt av ' . $hoursToUseThisMonth . ' denne måneden.'),

            Card::make('Timer igjen', $this->minutesToTime($totalMinutes - $tider->sum('totalt')))
                ->description('I gjennomsnitt ' . $avgPerWeek . ' i uka igjen')
                ->color('success'),

            Card::make('Antall utstyr på lista', Utstyr::all()->count()),
        ];
    }

    private function minutesToTime($minutes) : string
    {
        $hours   = $minutes / 60;
        $minutes = ($minutes % 60);
        $format  = '%02d:%02d';

        return sprintf($format, $hours, $minutes);
    }
}
