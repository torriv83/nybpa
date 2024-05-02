<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Timesheet;
use Carbon\Carbon;
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

        $timesheet = new Timesheet();

        /* Dette året */
        $thisYear      = $timesheet->TimeUsedThisYear();
        $thisYearTimes = $this->yearTimes($thisYear);

        /* Forrige år */
        $lastYear = Cache::tags(['timesheet'])->remember('lastYear', now()->addMonth(), function () {
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

        $yearTimes = [
            'januar' => '00.00',
            'februar' => '00.00',
            'mars' => '00.00',
            'april' => '00.00',
            'mai' => '00.00',
            'juni' => '00.00',
            'juli' => '00.00',
            'august' => '00.00',
            'september' => '00.00',
            'oktober' => '00.00',
            'november' => '00.00',
            'desember' => '00.00',
        ];

        foreach ($times as $key => $value) {
            if (array_key_exists($key, $yearTimes)) {
                $totalMinutes = $value->sum('totalt');
                $hours = intdiv($totalMinutes, 60);
                $minutes = $totalMinutes % 60;
                $yearTimes[$key] = sprintf('%02d', $hours) . '.' . sprintf('%02d', $minutes);
            }
        }

        return $yearTimes;
        
    }
}
