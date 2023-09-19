<?php

namespace App\Filament\Privat\Resources\EconomyResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;

class AccountsOverview extends BaseWidget
{
    protected static ?string $pollingInterval = null;

    protected function getCards(): array
    {

        $cards = collect();

        $ynab     = 'https://api.youneedabudget.com/v1/budgets/d7e4da92-0564-4e8f-87f5-c491ca545435/';
        $token    = config('app.ynab');
        $response = Http::withToken($token)->get($ynab . 'accounts/');
        $accounts = $response['data']['accounts'];

        //Inkluder kun bruks og spare kontoer
        $filteredAccounts = collect($accounts)->filter(function ($account) {
            return $account['type'] === 'checking' || $account['type'] === 'savings';
        });

        $filteredAccounts->each(function ($account) use ($cards) {
            $cards->push(
                Stat::make($account['name'], number_format(($account['cleared_balance'] / 1000), 0, ',', '.') . ' kr')
                    ->description('Sist avstemt: ' . Carbon::make($account['last_reconciled_at'])->diffForHumans())
            );
        });

        return $cards->toArray();
    }
}
