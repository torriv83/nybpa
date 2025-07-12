<?php

namespace App\Filament\Landslag\Resources\TestResultsResource\Widgets;

use App\Models\TestResults;
use App\Models\Tests;
use Filament\Widgets\ChartWidget;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class RheitChart extends ChartWidget
{
    protected static ?string $heading = 'Rheit test';

    protected static ?string $pollingInterval = null;

    protected static ?array $options = [];

    public function __construct()
    {
        self::$options = $this->getChartOptions();
    }

    /**
     * Retrieves the data for the chart.
     *
     * @return array<string, mixed> The formatted chart data.
     */
    protected function getData(): array
    {
        return Cache::tags(['testresult'])->remember('rheitChart', now()->addMonth(), function () {
            $rheit = $this->fetchData();

            if (! $rheit || $rheit->testResults->isEmpty()) {
                return $this->getDefaultChartData();
            }

            $transformedData = $this->transformData($rheit->testResults);

            return $this->prepareChartData($transformedData['resultater'], $transformedData['dato']);
        });
    }

    /**
     * Prepares the chart data for rendering.
     *
     * @param  array<int, array<string, float>>  $resultater
     * @param  array<int, string>  $dato
     * @return array<string, mixed>
     */
    protected function prepareChartData(array $resultater, array $dato): array
    {
        return $this->formatChartData($resultater, $dato);
    }

    /**
     * Retrieves the type of chart.
     *
     * @return string The type of chart.
     */
    protected function getType(): string
    {
        return 'line';
    }

    /**
     * Retrieves the data for the chart.
     *
     * @return Tests|null The fetched data for the chart.
     */
    protected function fetchData(): ?Tests
    {
        return Tests::with('testResults')->where('navn', '=', 'Rheit')->first();
    }

    /**
     * Transforms the given data into a specific format.
     *
     * @param  Collection<int, TestResults>  $results
     * @return array{resultater: array<int, array<string, float>>, dato: array<int, string>}
     */
    protected function transformData(Collection $results): array
    {
        $resultater = [];
        $dato = [];

        foreach ($results as $result) {
            $dato[] = $result->dato->format('d.m.y H:i');
            $this->transformResultat($resultater, $result->resultat[0]);
            $finalDrop = $this->calculateFinalDrop($result->resultat[0]);
            $resultater[] = ['Drop' => $finalDrop];
        }

        return [
            'resultater' => $resultater,
            'dato' => $dato,
        ];
    }

    /**
     * Transforms the given result into a specific format and appends it to the input array.
     *
     * @param  array<int, array<string, float>>  &$resultater
     * @param  array<string, float>  $resultat
     * @return array<int, array<string, float>>
     */
    protected function transformResultat(array &$resultater, array $resultat): array
    {
        foreach ($resultat as $name => $result) {
            $resultater[] = ['Runde: '.$name => $result];
        }

        return $resultater;
    }

    /**
     * Calculates the final drop of an array of results.
     *
     * @param  array<string, float>  $resultat
     */
    protected function calculateFinalDrop(array $resultat): float
    {
        return max($resultat) - min($resultat);
    }

    /**
     * Formats the chart data in a specific format.
     *
     * @param  array<int, array<string, float>>  $resultater
     * @param  array<int, string>  $dato
     * @return array<string, array<int, mixed>>
     */
    protected function formatChartData($resultater, $dato): array
    {
        $finalResults = [];
        $colors = TestResults::generateRandomColors(count(array_merge_recursive(...$resultater)));

        foreach (array_merge_recursive(...$resultater) as $name => $res) {
            $randColor = array_shift($colors);
            $res = count($dato) > 1 ? $res : [$res];
            $type = 'line';
            $finalResults[] = [
                'type' => $type,
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

    /**
     * Retrieves the default chart data.
     *
     * @return array<string, array<int, mixed>> Returns an array with the default chart data.
     */
    protected function getDefaultChartData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Rheit',
                    'data' => [],
                ],
            ],
            'labels' => [],
        ];
    }

    /**
     * Retrieves the options for the chart.
     *
     * @return array<string, mixed> Returns an array with the chart options.
     */
    private function getChartOptions(): array
    {
        return [
            'plugins' => [
                'tooltip' => [
                    'mode' => 'index',
                    'intersect' => false,
                ],
            ],
            'scales' => [
                'x' => [
                    'display' => true,
                    'title' => [
                        'display' => true,
                        'text' => 'Dato',
                    ],
                ],
                'y' => [
                    'display' => true,
                    'beginAtZero' => false,
                    'title' => [
                        'display' => true,
                        'text' => 'sekunder',
                    ],
                    'ticks' => [
                        'stepSize' => 0.2,
                    ],
                ],
            ],
        ];
    }
}
