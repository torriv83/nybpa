<?php

namespace App\Filament\Resources\EconomyResource\Widgets;

// use Filament\Widgets\Widget;

use App\Models\Economy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Filament\Widgets\StatsOverviewWidget\Card;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class StatsOverview extends BaseWidget
{
    // protected static string $view = 'filament.resources.economy-resource.widgets.stats-overview';
    protected static ?string $pollingInterval = null;

    protected function getCards(): array
    {

        $economy = Economy::first();

        $sumBeforeTax = ($economy->before_tax + $economy->grunnstonad) * 12;
        $sumAfterTax = ($economy->after_tax + $economy->grunnstonad) * 12;
        $percent = (($economy->before_tax - $economy->after_tax) * 100) / $economy->before_tax;

        $ynab = 'https://api.youneedabudget.com/v1/budgets/d7e4da92-0564-4e8f-87f5-c491ca545435/';
        $token = config('app.ynab');
        $response = Http::withToken($token)->get($ynab . 'months/' . 'current');
        $aom = $response['data']['month']['age_of_money'];

        return [
            Card::make('I året før skatt', number_format($sumBeforeTax, 0, ',', ' '))->description(number_format($sumBeforeTax / 12, 0, ',', ' ') . ' i måneden'),
            Card::make('I året etter skatt', number_format($sumAfterTax, 0, ',', ' '))->description(number_format($sumAfterTax / 12, 0, ',', ' ') . ' i måneden'),
            Card::make('Prosent skatt', number_format($percent) . '%'),
            Card::make('Age of money', $aom),
        ];
    }
}
