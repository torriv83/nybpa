<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Timesheet;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Cache;

class BrukteTimerChart extends ChartWidget
{
    protected static ?string $heading = 'Timer brukt hver måned';

    protected static ?string $pollingInterval = null;

    protected static ?int $sort = 2;

    protected function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {

        $timesheet = new Timesheet;

        /* Dette året */
        $thisYear = $timesheet->TimeUsedThisYear();
        $thisYearTimes = $this->yearTimes($thisYear);

        /* Forrige år */
        $lastYear = Cache::tags(['timesheet'])->remember('lastYear', now()->addMonth(), function () {
            return Timesheet::lastYear('fra_dato')
                ->orderByRaw('fra_dato ASC')
                ->where('unavailable', '=', 0)
                ->get()
                ->groupBy(fn ($val): string => Carbon::parse($val->fra_dato)->isoFormat('MMMM'));
        });

        $lastYearTimes = $this->yearTimes($lastYear);

        return [
            'datasets' => [
                [
                    'label' => Carbon::now()->format('Y'),
                    'data' => $thisYearTimes,
                    'tension' => 0.4,
                    'fill' => 'origin',
                    'backgroundColor' => 'rgba(255,153,0,0.6)',
                ],
                [
                    'label' => Carbon::now()->subYear()->format('Y'),
                    'data' => $lastYearTimes,
                    'tension' => 0.4,
                    'fill' => 'origin',
                    'backgroundColor' => 'rgba(153,255,51,0.3)',
                ],
            ],
        ];
    }

    public function yearTimes($times): array
    {

        // Create a period covering all months of the current year
        $period = CarbonPeriod::create('first day of January this year', '1 month', 'last day of December this year');

        $yearTimes = [];
        foreach ($period as $dt) {

            $monthName = $dt->isoFormat('MMMM');

            $yearTimes[$monthName] = '00.00';
        }

        foreach ($times as $key => $value) {
            if (array_key_exists($key, $yearTimes)) {
                $totalMinutes = $value->sum('totalt');
                $hours = intdiv($totalMinutes, 60);
                $minutes = $totalMinutes % 60;
                $yearTimes[$key] = sprintf('%02d', $hours).'.'.sprintf('%02d', $minutes);
            }
        }

        return $yearTimes;

    }
}
