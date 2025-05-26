<?php

namespace App\Filament\Privat\Resources\EconomyResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;

class AccountsOverview extends BaseWidget
{
    protected static ?string $pollingInterval = null;

    protected int|string|array $columnSpan = '12';

    /**
     * @throws ConnectionException
     */
    protected function getCards(): array
    {

        /** @var \Illuminate\Support\Collection<int, \Filament\Widgets\StatsOverviewWidget\Stat> $cards */
        $cards = collect();

        $ynab = 'https://api.youneedabudget.com/v1/budgets/d7e4da92-0564-4e8f-87f5-c491ca545435/';
        $token = config('app.ynab');
        $response = Http::withToken($token)->get($ynab.'accounts/');
        /** @var array<int, array<string, mixed>> $accounts */
        $accounts = $response['data']['accounts'];

        // Inkluder kun bruks og spare kontoer
        /** @var \Illuminate\Support\Collection<int, array<string, mixed>> $filteredAccounts */
        $filteredAccounts = collect($accounts)->filter(function (array $account) {
            return $account['type'] === 'checking' || $account['type'] === 'savings';
        });

        $filteredAccounts->each(function ($account) use ($cards) {
            $cards->push(
                Stat::make($account['name'], number_format(($account['cleared_balance'] / 1000), 0, ',', '.').' kr')
                    ->description('Sist avstemt: '.Carbon::make($account['last_reconciled_at'])->diffForHumans().
                        ', Balanse: '.number_format(($account['balance'] / 1000), 0, ',', '.'))
            );
        });

        return $cards->toArray();
    }
}
