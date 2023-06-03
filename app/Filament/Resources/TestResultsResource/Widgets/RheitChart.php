<?php

namespace App\Filament\Resources\TestResultsResource\Widgets;

use App\Models\Tests;
use Filament\Widgets\LineChartWidget;
use Illuminate\Support\Facades\Cache;

class RheitChart extends LineChartWidget
{
    protected static ?string $heading         = 'Rheit test';
    protected static ?string $pollingInterval = null;

    protected static ?array $options = [];

    public function __construct()
    {
        parent::__construct();

        self::$options = $this->getChartOptions();
    }

    protected function getData(): array
    {
        $rheit = $this->fetchData();

        if (!$rheit || $rheit->testResults->isEmpty()) {
            return $this->getDefaultChartData();
        }

        return $this->formatChartData(
            $this->transformData($rheit->testResults)['resultater'],
            $this->transformData($rheit->testResults)['dato']
        );
    }

    protected function fetchData()
    {
        return Cache::remember('rheitTest', now()->addDay(), function () {
            return Tests::with('testResults')->where('navn', '=', 'Rheit')->first();
        });
    }

    protected function transformData($results): array
    {
        $resultater = [];
        $dato       = [];
        $drop       = [];

        foreach ($results as $v) {
            $dato[] = $v->dato->format('d.m.y H:i');

            foreach ($v->resultat[0] as $name => $result) {
                $resultater[] = ['Runde: ' . $name => $result];
                $drop[]       = $result;
            }

            $finalDrop    = max($drop) - min($drop);
            $resultater[] = ['Drop' => $finalDrop];
        }

        return [
            'resultater' => $resultater,
            'dato'       => $dato,
        ];
    }

    protected function formatChartData($resultater, $dato): array
    {
        $finalResults = [];
        $colors       = generateRandomColors(count(array_merge_recursive(...$resultater)));

        foreach (array_merge_recursive(...$resultater) as $name => $res) {
            $randColor      = array_shift($colors);
            $res            = count($dato) > 1 ? $res : [$res];
            $type           = 'line';
            $finalResults[] = [
                'type'            => $type,
                'label'           => $name,
                'data'            => $res,
                'backgroundColor' => $randColor,
                'borderColor'     => $randColor,
                'borderWidth'     => 1,
            ];
        }

        return [
            'datasets' => $finalResults,
            'labels'   => $dato,
        ];
    }

    protected function getDefaultChartData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Rheit',
                    'data'  => [],
                ],
            ],
            'labels'   => [],
        ];
    }

    private function getChartOptions(): array
    {
        return [
            'plugins' => [
                'tooltip' => [
                    'mode'      => 'index',
                    'intersect' => false
                ],
            ],
            'scales'  => [
                'x' => [
                    'display' => true,
                    'title'   => [
                        'display' => true,
                        'text'    => 'Dato'
                    ]
                ],
                'y' => [
                    'display'     => true,
                    'beginAtZero' => false,
                    'title'       => [
                        'display' => true,
                        'text'    => 'sekunder',
                    ],
                    'ticks'       => [
                        'stepSize' => 0.2
                    ]
                ]
            ]
        ];
    }

}
