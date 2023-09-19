<?php

namespace App\Filament\Landslag\Resources\TestResultsResource\Widgets;

use App\Models\Tests;
use Filament\Widgets\ChartWidget;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class StyrkeChart extends ChartWidget
{
    protected static ?string $heading = 'Styrke tester';

    protected static ?string $pollingInterval = null;

    protected static ?array $options = [];

    public function __construct()
    {
        self::$options = $this->getChartOptions();
    }

    protected function getData(): array
    {

        return Cache::tags(['testresult'])->remember('styrkeChart', now()->addMonth(), function () {
            $styrketest = $this->fetchData();

            if (!$styrketest || $styrketest->testResults->isEmpty()) {
                return $this->getDefaultChartData();
            }

            $transformedData = $this->transformData($styrketest->testResults);
            $resultater      = $transformedData['resultater'];
            $dato            = $transformedData['dato'];

            return $this->formatChartData($resultater, $dato);

        });
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function fetchData(int $numberOfResults = 6)
    {

        return Tests::with([
            'testResults' => function ($query) use ($numberOfResults) {
                return $query->orderBy('dato', 'desc') // Order by date in descending order to get the latest results
                    ->take($numberOfResults); // Take only the specified number of results
            }
        ])
            ->where('navn', '=', 'Styrketest')
            ->first();

    }

    protected function transformData(Collection $results): array
    {
        $resultater = [];
        $dato       = [];

        foreach ($results as $v) {
            $dato[] = $v->dato->format('d.m.y H:i');

            foreach ($v->resultat[0] as $name => $result) {
                $resultater[$name][] = $result;
            }
        }

        return [
            'resultater' => $resultater,
            'dato'       => $dato,
        ];
    }

    protected function formatChartData(array $resultater, array $dato): array
    {
        $finalResults = [];
        $randColors   = generateRandomColors(count($resultater));

        foreach ($resultater as $name => $res) {
            $randColor = array_shift($randColors);
            $res       = count($dato) > 1 ? $res : [$res];

            $finalResults[] = [
                'type'            => 'line',
                'label'           => $name,
                'data'            => $res,
                'backgroundColor' => $randColor,
                'borderColor'     => $randColor,
                'borderWidth'     => 2,
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
                    'label' => 'Styrke',
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
                    'mode'      => 'nearest',
                    'intersect' => false,
                    'displayColors' => false,
                ],
            ],
            // Additional options
            // ...
        ];
    }
}
