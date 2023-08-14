<?php

namespace App\Filament\Widgets;

use App\Models\Timesheet;
use Carbon\Carbon;
use Filament\Widgets\LineChartWidget;
use Illuminate\Support\Facades\Cache;

class BrukteTimerChart extends LineChartWidget
{
    protected static ?string $heading = 'Timer brukt hver måned';

    protected static ?string $pollingInterval = null;

    protected static ?int $sort = 1;

    //protected int|string|array $columnSpan = 'col-span-3 sm:col-span-3 md:col-span-3 lg:col-span-3';
    protected int|string|array $columnSpan = 3;


    protected function getData(): array
    {

        $timesheet = new Timesheet();

        /* Dette året */
        $thisYear      = $timesheet->TimeUsedThisYear();
        $thisYearTimes = $this->yearTimes($thisYear);

        /* Forrige år */
        $lastYear = Cache::remember('lastYear', now()->addDay(), function () {
            return Timesheet::lastYear('fra_dato')
                ->orderByRaw('fra_dato ASC')
                ->get()
                ->groupBy(fn($val): string => Carbon::parse($val->fra_dato)->isoFormat('MMMM'));
        });

        $lastYearTimes = $this->yearTimes($lastYear);

        return [
            'datasets' => [
                [
                    'label'           => Carbon::now()->format('Y'),
                    'data'            => $thisYearTimes,
                    'tension'         => 0.4,
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

    public function yearTimes($times): array
    {

        $yearTimes = [];

        foreach ($times as $key => $value) {

            $yearTimes[$key] = sprintf('%02d', intdiv($value->sum('totalt'), 60)) . '.' . (sprintf('%02d', $value->sum('totalt') % 60));

        }

        return $yearTimes;

    }
}
