<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Settings;
use App\Models\Timesheet;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Pipeline;

class TimerChart extends ChartWidget
{
    protected static ?string $heading = 'Brukte timer av totalen (%)';

    protected static ?string $pollingInterval = null;

    protected static ?int $sort = 3;

    protected mixed $bpa;

    protected function getType(): string
    {
        return 'bar';
    }

    public function __construct()
    {
        $this->bpa = Settings::getUserBpa();
    }

    /**
     * Retrieves data for the chart.
     *
     * @uses Timesheet::timeUsedThisYear
     * @uses Timesheet::timeUsedLastYear
     */
    protected function getData(): array
    {
        $timeSheet = new Timesheet;
        $timeUsedThisYear = $timeSheet->timeUsedThisYear();

        /* Forrige år */
        $lastYearTimes = Pipeline::send($timeSheet)
            ->through([
                function ($timeSheet) {
                    return $this->usedTime($timeSheet->timeUsedLastYear());
                },
            ])
            ->then(fn ($timeSheet): string => $timeSheet);

        /* Dette året */
        $thisYearTimes = Pipeline::send($timeSheet)
            ->through([
                function () use ($timeUsedThisYear) {
                    return $this->usedTime($timeUsedThisYear);
                },
            ])
            ->then(fn ($timeSheet): string => $timeSheet);

        /* Gjenstår */
        $thisYearLeft = $this->usedTime($timeUsedThisYear, true);

        /* Datasets Chartjs */
        return [
            'datasets' => [
                [
                    'label' => Carbon::now()->subYear()->format('Y'),
                    'data' => $lastYearTimes,
                    'backgroundColor' => 'rgba(201, 203, 207, 0.2)',
                    'borderColor' => 'rgb(201, 203, 207)',
                    'borderWidth' => 1,
                ],
                [
                    'label' => Carbon::now()->format('Y'),
                    'data' => $thisYearTimes,
                    'backgroundColor' => 'rgba(54, 162, 235, 0.2)',
                    'borderColor' => 'rgb(54, 162, 235)',
                    'borderWidth' => 1,
                ],
                [
                    'type' => 'line',
                    'label' => 'Gjenstår',
                    'data' => $thisYearLeft,
                    'backgroundColor' => 'rgba(255, 99, 132, 0.2)',
                    'borderColor' => 'rgb(255, 99, 132)',
                    'borderWidth' => 1,
                ],
            ],

        ];
    }

    public function usedTime($times, bool $prosent = false): array
    {
        // Opprett en periode som inkluderer alle måneder av inneværende år
        $period = CarbonPeriod::create('first day of January this year', '1 month', 'last day of December this year');

        $sum = 0; // Holder styr på totalen over tid
        $tider = [];

        // Initialiser alle månedene med standardverdien 0
        foreach ($period as $dt) {
            $monthName = $dt->isoFormat('MMMM'); // Inkluder månedsnavn
            $tider[$monthName] = 0; // Standard verdi for måneder uten data
        }

        // Behandle dataene fra $times og akkumuler summen
        foreach ($times as $key => $value) {
            if (array_key_exists($key, $tider)) {
                $number = count($value);

                for ($i = 0; $i < $number; $i++) {
                    $sum += $value[$i]->totalt; // Akkumulerer totalen
                }

                // Beregn prosenten eller setter verdien.
                if ($prosent) {
                    $tider[$key] = 100 - round($sum / (($this->bpa * 52) * 60) * 100, 1);
                } else {
                    $tider[$key] = round($sum / (($this->bpa * 52) * 60) * 100, 1);
                }
            }
        }

        return $tider;
    }
}
