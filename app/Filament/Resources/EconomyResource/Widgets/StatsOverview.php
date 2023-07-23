<?php

namespace App\Filament\Resources\EconomyResource\Widgets;

use App\Models\Economy;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use Illuminate\Support\Facades\Http;

class StatsOverview extends BaseWidget
{

    protected static ?string $pollingInterval = null;

    protected $listeners = ['updateStatsOverview' => '$refresh'];

    protected function getCards(): array
    {
        $economy = Economy::first();

        $cards = [];

        if ($economy !== null) {
            $sumBeforeTax = ($economy->before_tax + $economy->grunnstonad) * 12;
            $sumAfterTax  = ($economy->after_tax + $economy->grunnstonad) * 12;
            $percent      = (($economy->before_tax - $economy->after_tax) * 100) / $economy->before_tax;

            $ynab     = 'https://api.youneedabudget.com/v1/budgets/d7e4da92-0564-4e8f-87f5-c491ca545435/';
            $token    = config('app.ynab');
            $response = Http::withToken($token)->get($ynab . 'months/' . 'current');
            
            $aom = $response['data']['month']['age_of_money'];

            $cards[] = Card::make('I året før skatt', number_format($sumBeforeTax, 0, ',', '.') . ' kr')
                ->description(number_format($sumBeforeTax / 12, 0, ',', '.') . ' kr i måneden');
            $cards[] = Card::make('I året etter skatt', number_format($sumAfterTax, 0, ',', '.') . ' kr')
                ->description(number_format($sumAfterTax / 12, 0, ',', '.') . ' kr i måneden');
            $cards[] = Card::make('Prosent skatt', number_format($percent) . '%');
            $cards[] = Card::make('Age of money', $aom);
        } else {
            // Handle the case when the economy record is not found
            // You can add an appropriate error card or take any other action
            $cards[] = Card::make('Error', 'Economy record not found');
        }

        return $cards;
    }

}
