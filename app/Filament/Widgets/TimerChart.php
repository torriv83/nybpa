<?php

namespace App\Filament\Widgets;

use DateTimeZone;
use Carbon\Carbon;
use App\Models\Timesheet;
use App\Settings\BpaTimer;
use Filament\Widgets\BarChartWidget;
use Barryvdh\Debugbar\Facades\Debugbar;

class TimerChart extends BarChartWidget
{
    protected static ?string $heading = 'Brukte timer av totalen (%)';
    protected static ?string $pollingInterval = null;
    protected static ?int $sort = 2;
    protected array|string|int $columnSpan = 6;

    // protected static ?array $options = [
    //     'plugins' => [
    //         'tooltip' => [
    //             'backgroundColor' => 'rgba(2, 6, 3, 0.9)',
    //             'callbacks' => [
    //                 'label' => function(){},
    //             ]
    //         ],
    //     ],
    // ];

    protected function getData(): array
    {
        $t = new Timesheet();

        /* Forrige år */
        $tid = $t->timeUsedLastYear();
        $tider = [];
        $sum = 0;

        foreach ($tid as $key => $value) {
            // $tider[$key] = $value->sum('totalt');

            for ($i = 0; $i < count($value); $i++) {
                $sum += $value[$i]->totalt;
            }

            $tider[$key] = round($sum / ((app(BpaTimer::class)->timer * 52) * 60) * 100, 1);
        }


        /* Dette året */
        $thisYear = $t->timeUsedThisYear();

        $thisYearTimes = [];
        $sum = 0;
        foreach ($thisYear as $key => $value) {

            for ($i = 0; $i < count($value); $i++) {
                $sum += $value[$i]->totalt;
            }
            // $thisYearTimes[$key] = $value->sum('totalt');
            $thisYearTimes[$key] = round($sum / ((app(BpaTimer::class)->timer * 52) * 60) * 100, 1);
        }


        /* Gjenstår */
        $sum = 0;

        foreach ($thisYear as $key => $value) {

            for ($i = 0; $i < count($value); $i++) {
                $sum += $value[$i]->totalt;
            }
            // $thisYearLeft[$key] = 100 - $sum;
            $thisYearLeft[$key] = 100 - round($sum / ((app(BpaTimer::class)->timer * 52) * 60) * 100, 1);
            $prosentIgjen = 100 - round($sum / ((app(BpaTimer::class)->timer * 52) * 60) * 100, 1);
        }
        //  debug($prosentIgjen);

        if ($prosentIgjen) {
            $prosentIgjen = $prosentIgjen;
        } else {
            $prosentIgjen = 'Ingen data';
        }

        /* Datasets Chartjs */
        return [
            'datasets' => [
                [
                    'label' => Carbon::now()->subYear()->format('Y'),
                    'data' => $tider,
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
                    'label' => 'Gjenstår: ' . $prosentIgjen . '%',
                    'data' => $thisYearLeft,
                    'backgroundColor' => 'rgba(255, 99, 132, 0.2)',
                    'borderColor' => 'rgb(255, 99, 132)',
                    'borderWidth' => 1
                ],
            ],

        ];
    }
}
