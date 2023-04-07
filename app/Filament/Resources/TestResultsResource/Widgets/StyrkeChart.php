<?php

namespace App\Filament\Resources\TestResultsResource\Widgets;

use App\Models\TestResults;
use App\Models\Tests;
use Filament\Widgets\LineChartWidget;
use Illuminate\Support\Facades\Cache;

class StyrkeChart extends LineChartWidget
{

    protected static ?string $heading         = 'Styrke tester';
    protected static ?string $pollingInterval = null;

    protected static ?array $options = [
        'plugins' => [
            'tooltip' => [
                'mode'      => 'index',
                'intersect' => false
            ],
        ]
    ];

    protected function getData(): array
    {

        //Hente ut data fra DB
        $styrketest = Cache::remember('styrkeChart', now()->addDay(), function () {
            return Tests::where('navn', '=', 'Styrketest')->get();
        });
        $resultat   = Cache::remember('styrkeResultat', now()->addDay(), function () use ($styrketest) {
            return TestResults::where('testsID', '=', $styrketest['0']->id)->orderBy('dato')->get();
        });

        //Define variables
        $dato       = [];
        $resultater = [];

        if (count($resultat) > 0) {

            //Bygg opp arrayer med dato og resultater
            foreach ($resultat as $v) {
                $dato[] = $v->dato->format('d.m.y H:i');
                foreach ($v->resultat[0] as $name => $result) {
                    $resultater[] = [$name => $result];
                }
            }

            //Sett sammen arrayene i formatet som trengs for chart.js
            $finalResults = [];
            foreach (array_merge_recursive(...$resultater) as $name => $res) {
                $randColor      = 'rgb(' . rand(0, 255) . ', ' . rand(0, 255) . ', ' . rand(0, 255) . ')';
                $res            = count($dato) > 1 ? $res : [$res];
                $finalResults[] = [
                    'label'           => $name,
                    'data'            => $res,
                    'backgroundColor' => $randColor,
                    'borderColor'     => $randColor,
                ];
            }

            //Voila!
            return [
                'datasets' => $finalResults,
                'labels'   => $dato,
            ];
        } else {
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
    }
}
