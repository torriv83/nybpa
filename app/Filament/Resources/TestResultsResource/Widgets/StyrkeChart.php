<?php

namespace App\Filament\Resources\TestResultsResource\Widgets;

use App\Models\Tests;
use Filament\Widgets\LineChartWidget;
use Illuminate\Support\Facades\Cache;

class StyrkeChart extends LineChartWidget
{
    protected static ?string $heading = 'Styrke tester';

    protected static ?string $pollingInterval = null;

    protected static ?array $options = [];

    public function __construct()
    {
        parent::__construct();

        self::$options = $this->getChartOptions();
    }

    protected function getData(): array
    {
        $styrketest = $this->fetchData();

        if (! $styrketest || $styrketest->testResults->isEmpty()) {
            return $this->getDefaultChartData();
        }

        $transformedData = $this->transformData($styrketest->testResults);
        $resultater = $transformedData['resultater'];
        $dato = $transformedData['dato'];

        return $this->formatChartData($resultater, $dato);
    }

    protected function fetchData()
    {
        return Cache::remember('styrkeChart', now()->addDay(), function () {
            return Tests::with('testResults')->where('navn', '=', 'Styrketest')->first();
        });
    }

    protected function transformData($results): array
    {
        $resultater = [];
        $dato = [];

        foreach ($results as $v) {
            $dato[] = $v->dato->format('d.m.y H:i');

            foreach ($v->resultat[0] as $name => $result) {
                $resultater[$name][] = $result;
            }
        }

        return [
            'resultater' => $resultater,
            'dato' => $dato,
        ];
    }

    protected function formatChartData(array $resultater, array $dato): array
    {
        $finalResults = [];
        $randColors = generateRandomColors(count($resultater));

        foreach ($resultater as $name => $res) {
            $randColor = array_shift($randColors);
            $res = count($dato) > 1 ? $res : [$res];

            $finalResults[] = [
                'type' => 'line',
                'label' => $name,
                'data' => $res,
                'backgroundColor' => $randColor,
                'borderColor' => $randColor,
                'borderWidth' => 1,
            ];
        }

        return [
            'datasets' => $finalResults,
            'labels' => $dato,
        ];
    }

    protected function getDefaultChartData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Styrke',
                    'data' => [],
                ],
            ],
            'labels' => [],
        ];
    }

    private function getChartOptions(): array
    {
        return [
            'plugins' => [
                'tooltip' => [
                    'mode' => 'index',
                    'intersect' => false,
                ],
            ],
            // Additional options
            // ...
        ];
    }
}
