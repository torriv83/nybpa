<?php

namespace App\Filament\Widgets;

use Carbon\Carbon;
use App\Models\Timesheet;
use Filament\Widgets\LineChartWidget;

class BrukteTimerChart extends LineChartWidget
{
    protected static ?string $heading = 'Timer brukt hver måned';
    protected static ?string $pollingInterval = null;
    protected static ?int $sort = 2;
    protected array|string|int $columnSpan = 4;
    
    protected function getData(): array
    {
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
            $thisYearTimes[$key] = sprintf('%02d',intdiv($value->sum('totalt'), 60)) .'.'. ( sprintf('%02d',$value->sum('totalt') % 60));//$value->sum('totalt');
            // $thisYearTimes[$key] = round($sum / 21900 * 100, 1);
        }


        /* Forrige år */
        $lastYear = Timesheet::whereBetween('fra_dato', [Carbon::now()->subYear()->startOfYear()->format('Y-m-d H:i:s'), Carbon::now()->subYear()->endOfYear()])
            ->orderByRaw('fra_dato ASC')
            ->get()
            ->groupBy(function ($val) {
                return Carbon::parse($val->fra_dato)->isoFormat('MMM');
            });
        
        $lastYearTimes = [];
        $sum = 0;

        foreach ($lastYear as $key => $value) {

            for ($i = 0; $i < count($value); $i++) {
                $sum += $value[$i]->totalt;
            }
            $lastYearTimes[$key] = sprintf('%02d',intdiv($value->sum('totalt'), 60)) .'.'. ( sprintf('%02d',$value->sum('totalt') % 60));//$value->sum('totalt');

        }

        return [
            'datasets' => [
                [
                    'label' => Carbon::now()->subYear()->format('Y'),
                    'data' => $lastYearTimes,
                    'backgroundColor' => '#cccccc',
                ],
                [
                    'label' => Carbon::now()->format('Y'),
                    'data' => $thisYearTimes,
                    'backgroundColor' => '#3758FE',
                ],
            ],
        ];
    }
}
