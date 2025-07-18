<?php

namespace App\Filament\Landslag\Resources\TestResultsResource\Widgets;

use App\Models\TestResults;
use App\Models\Tests;
use Filament\Widgets\ChartWidget;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class VektChart extends ChartWidget
{
    protected static ?string $heading = 'Vekt';

    protected static ?string $pollingInterval = null;

    protected static ?array $options = [];

    public function __construct()
    {
        self::$options = $this->getChartOptions();
    }

    protected function getData(): array
    {
        return Cache::tags(['testresult'])->remember('vektChart', now()->addMonth(), function () {
            $weights = [];
            $dates = [];

            // Hent første test (eller null hvis ingen)
            $firstTest = $this->getTests()->first();
            /** @var Tests|null $firstTest */
            if ($firstTest) {
                $results = $this->getTestResults($firstTest);

                foreach ($results as $result) {
                    $weights[] = $result->resultat[0]['Vekt'];
                    $dates[] = $result->dato->format('d.m.y H:i');
                }
            }

            return [
                'datasets' => [
                    [
                        'label' => 'Vekt',
                        'data' => $weights,
                    ],
                ],
                'labels' => $dates,
            ];
        });
    }

    protected function getType(): string
    {
        return 'line';
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tests>
     */
    protected function getTests(): Collection
    {
        return Tests::where('navn', '=', 'Vekt')->get();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, \App\Models\TestResults>
     */
    protected function getTestResults(Tests $test): Collection
    {
        return TestResults::where('tests_id', '=', $test->id)
            ->orderBy('dato')
            ->get();
    }

    /**
     * @return array<string, mixed>
     */
    private function getChartOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
            'scales' => $this->getChartScales(),
        ];
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    private function getChartScales(): array
    {
        return [
            'x' => [
                'display' => true,
                'title' => [
                    'display' => true,
                    'text' => 'Dato',
                ],
            ],
            'y' => [
                'display' => true,
                'title' => [
                    'display' => true,
                    'text' => 'Kg',
                ],
            ],
        ];
    }
}
