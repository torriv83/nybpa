<?php

namespace App\Filament\Widgets;

use Carbon\Carbon;
use App\Models\Timesheet;
use Filament\Widgets\LineChartWidget;

class BrukteTimerChart extends LineChartWidget
{

    protected static ?string   $heading         = 'Timer brukt hver måned';
    protected static ?string   $pollingInterval = null;
    protected static ?int      $sort            = 2;
    protected array|string|int $columnSpan      = 6;

    protected function getData() : array
    {

        /* Dette året */
        $thisYear = Timesheet::whereBetween('fra_dato', [Carbon::parse('first day of January')->format('Y-m-d H:i:s'), Carbon::now()->endOfYear()])
                             ->where('unavailable', '!=', 1)
                             ->orderByRaw('fra_dato ASC')
                             ->get()
                             ->groupBy(fn($val) => Carbon::parse($val->fra_dato)->isoFormat('MMM'));

        $thisYearTimes = $this->yearTimes($thisYear);

        /* Forrige år */
        $lastYear = Timesheet::whereBetween('fra_dato', [Carbon::now()->subYear()->startOfYear()->format('Y-m-d H:i:s'), Carbon::now()->subYear()->endOfYear()])
                             ->orderByRaw('fra_dato ASC')
                             ->get()
                             ->groupBy(fn($val) => Carbon::parse($val->fra_dato)->isoFormat('MMM'));

        $lastYearTimes = $this->yearTimes($lastYear);

        return [
            'datasets' => [
                [
                    'label'           => Carbon::now()->format('Y'),
                    'data'            => $thisYearTimes,
                    'fill'            => 'origin',
                    'backgroundColor' => 'rgba(255,153,0,0.6)',
                ],
                [
                    'label'           => Carbon::now()->subYear()->format('Y'),
                    'data'            => $lastYearTimes,
                    'tension'         => 0.4,
                    'fill'            => 'origin',
                    'backgroundColor' => 'rgba(153,255,51,0.3)',
                ],
            ],
        ];
    }

    public function yearTimes($times) : array
    {

        $yearTimes = [];

        foreach ($times as $key => $value) {

            $yearTimes[$key] = sprintf('%02d', intdiv($value->sum('totalt'), 60)).'.'.(sprintf('%02d', $value->sum('totalt') % 60));

        }

        return $yearTimes;

    }
}
