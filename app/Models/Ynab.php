<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

/**
 * Class Ynab
 *
 * Represents the Ynab model, responsible for handling budget data fetched
 * from an external API and saving it into the application's database.
 * Provides mutators for specific attributes to convert raw data into
 * a human-readable format.
 */
class Ynab extends Model
{
    protected $fillable = ['month', 'activity', 'income', 'budgeted'];

    protected $casts = [
        'month' => 'string',
    ];

    /**
     * @throws ConnectionException
     */
    public static function fetchData(): void
    {
        $ynab = 'https://api.ynab.com/v1/budgets/d7e4da92-0564-4e8f-87f5-c491ca545435/';
        $token = config('app.ynab');
        $products = Http::withToken($token)->get($ynab.'months')->json();

        $filteredData = Arr::map($products['data']['months'], function ($item) {
            return Arr::only($item, ['month', 'activity', 'income', 'budgeted']);
        });

        // Save to database
        foreach ($filteredData as $data) {
            Ynab::updateOrCreate(['month' => $data['month']], $data)->touch();
        }
    }

    public function getIncomeAttribute(int|float $value): float|int
    {
        return $value / 1000;
    }

    public function getActivityAttribute(int|float $value): float|int
    {
        return $value / 1000;
    }

    public function getBudgetedAttribute(int|float $value): float|int
    {
        return $value / 1000;
    }
}
