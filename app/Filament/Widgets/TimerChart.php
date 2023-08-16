<?php

namespace App\Filament\Widgets;

use App\Models\Settings;
use App\Models\Timesheet;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Pipeline;

class TimerChart extends ChartWidget
{
    protected static ?string $heading = 'Brukte timer av totalen (%)';

    protected static ?string $pollingInterval = null;

    protected static ?int $sort = 2;

    //protected int|string|array $columnSpan = 'col-span-3 sm:col-span-3 md:col-span-3 lg:col-span-3';
    protected int|string|array $columnSpan = 3;

    protected mixed $bpa;

    protected function getType(): string
    {
        return 'bar';
    }

    public function __construct()
    {
//        parent::__construct();

        $setting   = Settings::where('user_id', '=', Auth::id())->first();
        $this->bpa = $setting['bpa_hours_per_week'] ?? 1;
    }

    protected function getData(): array
    {
        $timeSheet        = new Timesheet();
        $timeUsedThisYear = $timeSheet->timeUsedThisYear();

        /* Forrige år */
        $lastYearTimes = Pipeline::send($timeSheet)
            ->through([
                function ($timeSheet) {
                    return $this->usedTime($timeSheet->timeUsedLastYear());
                },
            ])
            ->then(fn($timeSheet): string => $timeSheet);

        /* Dette året */
        $thisYearTimes = Pipeline::send($timeSheet)
            ->through([
                function ($timeSheet) use ($timeUsedThisYear) {
                    return $this->usedTime($timeUsedThisYear);
                },
            ])
            ->then(fn($timeSheet): string => $timeSheet);

        /* Gjenstår */
        $thisYearLeft = $this->usedTime($timeUsedThisYear, true);

        /* Datasets Chartjs */
        return [
            'datasets' => [
                [
                    'label'           => Carbon::now()->subYear()->format('Y'),
                    'data'            => $lastYearTimes,
                    'backgroundColor' => 'rgba(201, 203, 207, 0.2)',
                    'borderColor'     => 'rgb(201, 203, 207)',
                    'borderWidth'     => 1,
                ],
                [
                    'label'           => Carbon::now()->format('Y'),
                    'data'            => $thisYearTimes,
                    'backgroundColor' => 'rgba(54, 162, 235, 0.2)',
                    'borderColor'     => 'rgb(54, 162, 235)',
                    'borderWidth'     => 1,
                ],
                [
                    'type'            => 'line',
                    'label'           => 'Gjenstår',
                    'data'            => $thisYearLeft,
                    'backgroundColor' => 'rgba(255, 99, 132, 0.2)',
                    'borderColor'     => 'rgb(255, 99, 132)',
                    'borderWidth'     => 1,
                ],
            ],

        ];
    }

    public function usedTime($times, bool $prosent = false): array
    {
        $sum   = 0;
        $tider = [];
        foreach ($times as $key => $value) {
            $number = count($value);

            for ($i = 0; $i < $number; $i++) {
                $sum += $value[$i]->totalt;
            }

            if ($prosent == 1) {
                $tider[$key] = 100 - round($sum / (($this->bpa * 52) * 60) * 100, 1);
            } else {
                $tider[$key] = round($sum / (($this->bpa * 52) * 60) * 100, 1);
            }

        }

        return $tider;
    }
}
