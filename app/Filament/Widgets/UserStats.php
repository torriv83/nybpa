<?php

namespace App\Filament\Widgets;

//use DateTime;
use App\Models\Timesheet;
use App\Models\User as Users;
use App\Models\Utstyr;
use App\Settings\BpaTimer;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use Illuminate\Support\Facades\Cache;

class UserStats extends BaseWidget
{
    protected static ?string $pollingInterval = '600s';
    protected static ?int    $sort            = 1;

    protected function getColumns(): int
    {
        return 4;
    }

    protected function getCards(): array
    {
        $timesheet = new Timesheet();

        $tider = $timesheet->whereBetween(
            'fra_dato',
            [
                Carbon::parse('first day of January')
                    ->format('Y-m-d H:i:s'),
                Carbon::now()
            ]
        )->where('unavailable', '!=', '1');

        /* Timer i uka igjen */
        $ukerIgjen            = Carbon::parse(now())->floatDiffInWeeks(now()->endOfYear());
        $totaltTimerInnvilget = (app(BpaTimer::class)->timer * 52) * 60;

        // Kalkulasjoner
        $totalMinutes     = $totaltTimerInnvilget; // convert total hours to minutes
        $hoursUsedMinutes = Cache::remember('hoursUsedMinutes', now()->addDay(), function () use ($tider) {
            return $tider->sum('totalt');
        });

        // convert hours used to minutes
        $hoursPerWeek = 24 * 7;
        $leftPerWeek  = (($totalMinutes - $hoursUsedMinutes) / 60 - ($hoursPerWeek * $ukerIgjen)) / $ukerIgjen;

        $avgPerWeek = date('H:i:s', mktime(0, $leftPerWeek * 60));

        /* Chart timer brukt dette året */
        $thisYear = $timesheet->TimeUsedThisYear();

        $thisYearTimes = [];
        $sum           = 0;
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
        $usedThisMonth       = Cache::remember('usedThisMonth', now()->addDay(), function () use ($timesheet) {
            return $timesheet->thisMonth()->thisYear()->where('unavailable', '=', '0')->sum('totalt');
        });

        /* Card Widgets */
        return [
            Card::make('Antall Assistenter', Cache::remember('antallAssistenter', now()->addMonth(), function () {
                return Users::assistenter()->count();
            }))
                ->url(route('filament.resources.users.index')),

            Card::make('Timer brukt i år', Cache::remember('timerBruktiAar', now()->addDay(), function () use ($tider) {
                return $this->minutesToTime($tider->sum('totalt'));
            }))
                ->chart($thisYearTimes)
                ->color('success')
                ->url(route('filament.resources.timesheets.index',
                    'tableFilters[måned][fra_dato]=2023-01-01&tableFilters[måned][til_dato]=2023-12-31'))
                ->description($this->minutesToTime($usedThisMonth) . ' brukt av ' . $hoursToUseThisMonth . ' denne måneden.'),

            Card::make('Timer igjen', Cache::remember('timerIgjen', now()->addDay(), function () use ($tider, $totalMinutes) {
                return $this->minutesToTime($totalMinutes - $tider->sum('totalt'));
            }))
                ->description('I gjennomsnitt ' . $avgPerWeek . ' i uka igjen')
                ->color('success'),

            Card::make('Antall utstyr på lista', Cache::remember('antallUtstyr', now()->addDay(), function () {
                return Utstyr::all()->count();
            })),
        ];
    }

    private function minutesToTime($minutes): string
    {
        $hours   = $minutes / 60;
        $minutes = ($minutes % 60);
        $format  = '%02d:%02d';

        return sprintf($format, $hours, $minutes);
    }
}
