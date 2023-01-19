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
    protected array|string|int $columnSpan = 4;

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

        /* Forrige år */
        $tid = Timesheet::whereBetween('fra_dato', [Carbon::now()->subYear()->startOfYear()->format('Y-m-d H:i:s'), Carbon::now()->subYear()->endOfYear()])
            ->where('unavailable', '!=', 1)
            ->orderByRaw('fra_dato ASC')
            ->get()
            ->groupBy(function ($val) {
                return Carbon::parse($val->fra_dato)->isoFormat('MMM');
            });

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
                    'backgroundColor' => '#CCCCCC',
                ],
                [
                    'label' => Carbon::now()->format('Y'),
                    'data' => $thisYearTimes,
                    'backgroundColor' => '#3758FE',
                ],
                [
                    'label' => 'Gjenstår: ' . $prosentIgjen . '%',
                    'data' => $thisYearLeft,
                    'backgroundColor' => '#006400',
                ],
            ],

        ];
    }
}
