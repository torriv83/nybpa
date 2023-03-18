<?php

namespace App\Filament\Resources\TestResultsResource\Widgets;

use App\Models\Tests;
use App\Models\TestResults;
use Filament\Widgets\LineChartWidget;

class RheitChart extends LineChartWidget
{
    protected static ?string $heading = 'Rheit test';
    protected static ?string $pollingInterval = null;

    protected function getData(): array
    {

        $rheit = Tests::where('navn', '=', 'Rheit')->get();
        $resultat = TestResults::where('testsID', '=', $rheit['0']->id)->orderBy('dato', 'asc')->get();

        foreach ($resultat as $r => $v) {
            $dato[] = $v->dato->format('d.m.y H:i');
            foreach ($v->resultat[0] as $name => $result) {
                $resultater[] = array('Runde: ' . $name => $result);
            }
        }

        $finalResults = array();
        foreach (array_merge_recursive(...$resultater) as $name => $res) {

            $randColor = 'rgb(' . rand(0, 255) . ', ' . rand(0, 255) . ', ' . rand(0, 255) . ')';
            $res = count($dato) > 1 ? $res : [$res];
            $finalResults[] = array(
                'label' => $name,
                'data' => $res,
                'backgroundColor' => $randColor,
                'borderColor' => $randColor,
            );
        }
        return [
            'datasets' => $finalResults,
            'labels' => $dato,
        ];
    }
}
