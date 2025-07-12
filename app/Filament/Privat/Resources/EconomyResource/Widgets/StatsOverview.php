<?php

namespace App\Filament\Privat\Resources\EconomyResource\Widgets;

use App\Models\Economy;
use App\Traits\Economy as EconomyTrait;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Number;

class StatsOverview extends BaseWidget
{
    use EconomyTrait;

    protected static ?string $pollingInterval = null;

    protected int|string|array $columnSpan = '12';

    /**
     * @var array<string, string>
     */
    protected $listeners = ['updateStatsOverview' => '$refresh'];

    protected function getStats(): array
    {
        $economy = Economy::first();

        $cards = [];

        if ($economy !== null) {
            $cards[] = $this->createBeforeTaxCard($economy);
            $cards[] = $this->createAfterTaxCard($economy);
            $cards[] = $this->createTaxPercentageCard($economy);
            $cards[] = $this->createAgeOfMoneyCard();
        } else {
            // Handle the case when the economy record is not found
            // You can add an appropriate error card or take any other action
            $cards[] = Stat::make('Error', 'Economy record not found');
        }

        return $cards;
    }

    /**
     * Creates a before-tax card.
     *
     * @param  mixed  $economy  The economy object.
     * @return Stat The created stat object.
     */
    private function createBeforeTaxCard(mixed $economy): Stat
    {
        $sumBeforeTax = ($economy->before_tax + $economy->grunnstonad) * 12;

        return Stat::make('I året før skatt', self::formatCurrency($sumBeforeTax))
            ->description(self::formatCurrency($sumBeforeTax, true));
    }

    /**
     * Creates an after-tax card.
     *
     * @param  mixed  $economy  the economy object
     * @return Stat the created after-tax card
     */
    private function createAfterTaxCard(mixed $economy): Stat
    {
        $sumAfterTax = ($economy->after_tax + $economy->grunnstonad) * 12;

        return Stat::make('I året etter skatt', self::formatCurrency($sumAfterTax))
            ->description(self::formatCurrency($sumAfterTax, true));
    }

    /**
     * Calculates the tax percentage from the before-tax and after-tax values.
     *
     * @param  mixed  $economy  the object containing the before-tax and after-tax values
     * @return Stat the created tax percentage card
     */
    private function createTaxPercentageCard(mixed $economy): Stat
    {
        $percent = (($economy->before_tax - $economy->after_tax) * 100) / $economy->before_tax;

        return Stat::make('Prosent skatt', Number::percentage($percent));
    }

    /**
     * Creates an Age of Money card.
     *
     * @return Stat The created Age of Money card.
     */
    private function createAgeOfMoneyCard(): Stat
    {
        $ynab = 'https://api.youneedabudget.com/v1/budgets/d7e4da92-0564-4e8f-87f5-c491ca545435/';
        $token = config('app.ynab');
        $response = Http::withToken($token)->get($ynab.'months/'.'current');

        $aom = $response['data']['month']['age_of_money'];

        return Stat::make('Age of money', $aom);
    }
}
