<?php

namespace App\Filament\Resources\TestResultsResource\Widgets;

use Filament\Widgets\LineChartWidget;
use App\Models\TestResults;
use App\Models\Tests;

class VektChart extends LineChartWidget
{
    protected static ?string $heading = 'Vekt';
    protected static ?string $pollingInterval = null;

    protected static ?array $options = [
        'plugins' => [
            'legend' => [
                'display' => false,
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
                'title' => [
                    'display' => true,
                    'text' => 'Kg'
                ]
            ]
        ]
    ];

    protected function getData(): array
    {

        $value = [];
        $date = [];
        $vekt = Tests::where('navn', '=', 'Vekt')->get();
        $resultat = TestResults::where('testsID', '=', $vekt['0']->id)->orderBy('dato', 'asc')->get();

        if ($resultat) {

            foreach ($resultat as $r => $v) {
                $value[$r] = $v->resultat[0]['Vekt'];
                $date[$r] = $v->dato->format('d.m.y H:i');
            }

            return [
                'datasets' => [
                    [
                        'label' => 'Vekt',
                        'data' => $value,
                    ],
                ],
                'labels' => $date,
            ];
        } else {
            return 'Ingen vekt registrert enda.';
        }
    }
}
