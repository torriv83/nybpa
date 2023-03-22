<?php

namespace App\Filament\Widgets;

//use DateTimeZone;
use Carbon\Carbon;
use App\Models\Timesheet;
use App\Settings\BpaTimer;
use Filament\Widgets\BarChartWidget;
//use Barryvdh\Debugbar\Facades\Debugbar;

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
        $timeSheet = new Timesheet();

        /* Forrige år */
        $tid = $timeSheet->timeUsedLastYear();
        $lastYearTimes = $this->usedTime($tid);

        /* Dette året */
        $thisYear = $timeSheet->timeUsedThisYear();
        $thisYearTimes = $this->usedTime($thisYear);

        /* Gjenstår */
        $thisYearLeft = $this->usedTime($thisYear, true);


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
                    'borderWidth' => 1
                ],
            ],

        ];
    }

    /**
     * @param $times
     * @param  bool  $prosent
     *
     * @return array
     */
    public function usedTime($times, bool $prosent = false) : array
    {
        $sum = 0;
        $tider = [];
        foreach ($times as $key => $value) {
            $number = count($value);

            for ($i = 0; $i < $number; $i++) {
                $sum += $value[$i]->totalt;
            }

            if($prosent == 1){
                $tider[$key] = 100 - round($sum / ((app(BpaTimer::class)->timer * 52) * 60) * 100, 1);
            }else{
                $tider[$key] = round($sum / ((app(BpaTimer::class)->timer * 52) * 60) * 100, 1);
            }

        }

        return $tider;
    }
}
