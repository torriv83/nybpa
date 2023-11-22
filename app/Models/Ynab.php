<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;


class Ynab extends Model
{

    protected $fillable = ['month', 'activity', 'income', 'budgeted'];

    protected $casts = [
        'month' => 'string',
    ];

    public static function fetchData()
    {
        $ynab     = 'https://api.youneedabudget.com/v1/budgets/d7e4da92-0564-4e8f-87f5-c491ca545435/';
        $token    = config('app.ynab');
        $products = Http::withToken($token)->get($ynab.'months')->json();

        $filteredData = Arr::map($products['data']['months'], function ($item)
        {
            return Arr::only($item, ['month', 'activity', 'income', 'budgeted']);
        });

        // Save to database
        foreach ($filteredData as $data)
        {
            Ynab::updateOrCreate(['month' => $data['month']], $data);
        }
    }

    public function getIncomeAttribute($value): float|int
    {
        return $value / 1000;
    }

    public function getActivityAttribute($value): float|int
    {
        return $value / 1000;
    }

    public function getBudgetedAttribute($value): float|int
    {
        return $value / 1000;
    }
}
