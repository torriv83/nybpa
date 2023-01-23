<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Sushi\Sushi;

class ynab extends Model
{
    use Sushi;
    use HasFactory;

    public function getRows()
    {
        //API
        // $products = Http::get('https://dummyjson.com/products')->json();
        $ynab = 'https://api.youneedabudget.com/v1/budgets/d7e4da92-0564-4e8f-87f5-c491ca545435/';
        $token = config('app.ynab');
        $products = Http::withToken($token)->get($ynab . 'months')->json();

        //filtering some attributes
        $products = Arr::map($products['data']['months'], function ($item) {
            return Arr::only(
                $item,
                [
                    'month',
                    'activity',
                    'income',
                    'budgeted',
                ]
            );
        });

        return $products;
    }
}
