<?php

namespace App\Filament\Resources\TestResultsResource\Widgets;

use App\Models\Tests;
use App\Models\TestResults;
use Filament\Widgets\LineChartWidget;

class StyrkeChart extends LineChartWidget
{
    protected static ?string $heading = 'Styrke tester';
    protected static ?string $pollingInterval = null;

    protected function getData(): array
    {
        //Hente ut data fra DB
        $styrketest = Tests::where('navn', '=', 'Styrketest')->get();
        $resultat = TestResults::where('testsID', '=', $styrketest['0']->id)->orderBy('dato', 'asc')->get();

        //Bygg opp arrayer med dato og resultater
        foreach ($resultat as $r => $v) {
            $dato[] = $v->dato->format('d.m.y H:i');
            foreach ($v->resultat[0] as $name => $result) {
                $resultater[] = array($name => $result);
            }
        }

        //Sett sammen arrayene i formatet som trengs for chart.js
        $finalResults = array();
        foreach (array_merge_recursive(...$resultater) as $name => $res) {
            $randColor = 'rgb(' . rand(0, 255) . ', ' . rand(0, 255) . ', ' . rand(0, 255) . ')';
            $finalResults[] = array(
                'label' => $name,
                'data' => $res,
                'backgroundColor' => $randColor,
                'borderColor' => $randColor,
            );
        }

        //Voila!
        return [
            'datasets' => $finalResults,
            'labels' => $dato,
        ];
    }
}
