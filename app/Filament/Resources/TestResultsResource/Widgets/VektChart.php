<?php

namespace App\Filament\Resources\TestResultsResource\Widgets;

use App\Models\TestResults;
use App\Models\Tests;
use Filament\Widgets\LineChartWidget;
use Illuminate\Support\Facades\Cache;

class VektChart extends LineChartWidget
{
    protected static ?string $heading         = 'Vekt';
    protected static ?string $pollingInterval = null;

    protected static ?array $options = [];

    public function __construct()
    {
        parent::__construct();

        self::$options = $this->getChartOptions();
    }

    protected function getData(): array
    {
        $weights = [];
        $dates   = [];

        try {
            $tests = $this->getTests();
            if ($tests) {
                $results = $this->getTestResults($tests->first());
                foreach ($results as $result) {
                    $weights[] = $result->resultat[0]['Vekt'];
                    $dates[]   = $result->dato->format('d.m.y H:i');
                }
            }
        } catch (\Exception $e) {
            // Handle the exception, log, or display an error message
        }

        return [
            'datasets' => [
                [
                    'label' => 'Vekt',
                    'data'  => $weights,
                ],
            ],
            'labels'   => $dates,
        ];
    }

    protected function getTests()
    {
        return Cache::remember('vektChart', now()->addDay(), function () {
            return Tests::where('navn', '=', 'Vekt')->get();
        });
    }

    protected function getTestResults($test)
    {
        return Cache::remember('vektResultat', now()->addDay(), function () use ($test) {
            return TestResults::where('testsID', '=', $test->id)
                ->orderBy('dato')
                ->get();
        });
    }

    private function getChartOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
            'scales'  => $this->getChartScales(),
        ];
    }

    private function getChartScales(): array
    {
        return [
            'x' => [
                'display' => true,
                'title'   => [
                    'display' => true,
                    'text'    => 'Dato'
                ]
            ],
            'y' => [
                'display' => true,
                'title'   => [
                    'display' => true,
                    'text'    => 'Kg'
                ]
            ]
        ];
    }
}
