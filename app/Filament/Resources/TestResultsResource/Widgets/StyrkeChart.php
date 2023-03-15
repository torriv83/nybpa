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

        $test = array();
        $label = array();
        foreach ($resultat as $r => $v) {

            foreach ($v->resultat as $k => $res) {

                foreach ($res as $name => $result) {

                    // $data['label'] = $name;
                    //Prøv å søk i variabelen $test istedenfor, etter at den er pushet
                    if (array_search($name, $label)) {
                        // dd(array_search($name, $label));
                        array_push($test[array_search($name, $label)]['data'], [$v->dato->format('d.m.y H:i') => $result]);
                    } else {
                        $data['label'] = $name;
                        $data['data'] = [$v->dato->format('d.m.y H:i') => $result];
                        $data['backgroundColor'] = 'rgb(' . rand(0, 255) . ', ' . rand(0, 255) . ', ' . rand(0, 255) . ')';
                    }
                    array_push($test, $data);

                    $label[] = $name;
                }
            }

            $date[$r] = $v->dato->format('d.m.y H:i');
        }
        dd($test);

        return [
            'datasets' => $test,
        ];
    }
}
