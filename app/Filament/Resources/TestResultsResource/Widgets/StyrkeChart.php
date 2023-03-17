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
        $styrketest = Tests::where('navn', '=', 'Styrketest')->get();
        $resultat = TestResults::where('testsID', '=', $styrketest['0']->id)->orderBy('dato', 'asc')->get();

        $tester = array();
        foreach ($resultat as $r => $v) {
            foreach ($v->resultat as $k => $res) {
                foreach ($res as $name => $result) {

                    if (array_search($name, array_column($tester, 'label'))) {
                        $tester[array_search($name, array_column($tester, 'label'))]['data'][$v->dato->format('d.m.y H:i')] = $result;
                    } else {
                        $tester[] = array(
                            'label' => $name,
                            'data' => array($v->dato->format('d.m.y H:i') => $result),
                            'backgroundColor' => 'rgb(' . rand(0, 255) . ', ' . rand(0, 255) . ', ' . rand(0, 255) . ')'
                        );
                    }
                }
            }
        }
        dd($tester); //DumpAndDie

        return [
            'datasets' => $tester,
        ];
    }
}
