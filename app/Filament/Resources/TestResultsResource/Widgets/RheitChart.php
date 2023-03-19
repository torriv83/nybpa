<?php

namespace App\Filament\Resources\TestResultsResource\Widgets;

use App\Models\Tests;
use App\Models\TestResults;
use Filament\Widgets\LineChartWidget;

class RheitChart extends LineChartWidget
{
    protected static ?string $heading = 'Rheit test';
    protected static ?string $pollingInterval = null;

    protected static ?array $options = [
        'plugins' => [
            'tooltip' => [
                'mode' => 'index',
                'intersect' => false
            ],
        ],
        'scales' => [
            'x' => [
                'display' => true,
                'title' => [
                    'display' => true,
                    'text' => 'Dato'
                ]
            ],
            'y' => [
                'display' => true,
                'beginAtZero' => false,
                'title' => [
                    'display' => true,
                    'text' => 'sekunder',
                ],
                'ticks' => [
                    'stepSize' => 0.2
                ]
            ]
        ]
    ];

    protected function getData(): array
    {
        //Hente ut data fra DB
        $rheit = Tests::where('navn', '=', 'Rheit')->get();
        $resultat = TestResults::where('testsID', '=', $rheit['0']->id)->orderBy('dato', 'asc')->get();

        if (count($resultat) > 0) {

            //Bygg opp arrayer med dato og resultater
            foreach ($resultat as $r => $v) {
                $dato[] = $v->dato->format('d.m.y H:i');

                foreach ($v->resultat[0] as $name => $result) {
                    $resultater[] = array('Runde: ' . $name => $result);
                    $drop[] = $result;
                }
                //Regn ut droppen fra beste-dårligste runde
                $finalDrop = max($drop) - min($drop);
                array_push($resultater, array('Drop' => $finalDrop));
            }

            //Sett sammen arrayene i formatet som trengs for chart.js
            $finalResults = array();

            foreach (array_merge_recursive(...$resultater) as $name => $res) {
                $randColor = 'rgb(' . rand(0, 255) . ', ' . rand(0, 255) . ', ' . rand(0, 255) . ')';
                $res = count($dato) > 1 ? $res : [$res];
                if ($name == 'Drop') {
                    $type = 'line';
                } else {
                    $type = 'line';
                }
                $finalResults[] = array(
                    'type' => $type,
                    'label' => $name,
                    'data' => $res,
                    'backgroundColor' => $randColor,
                    'borderColor' => $randColor,
                    'borderWidth' => 1,
                );
            }

            //Voila!
            return [
                'datasets' => $finalResults,
                'labels' => $dato,
            ];
        } else {
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
    }
}
