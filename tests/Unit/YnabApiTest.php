<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;

uses(RefreshDatabase::class);

it('get a response of 200', function () {
    // Fake YNAB response
    Http::fake([
        'api.youneedabudget.com/*' => Http::response([
            'data' => [],
        ], 200),
    ]);

    $ynab = 'https://api.youneedabudget.com/v1/budgets/d7e4da92-0564-4e8f-87f5-c491ca545435/';
    $token = 'test-token'; // denne brukes ikke når det er faked

    $response = Http::withToken($token)->get($ynab.'accounts/');

    expect($response['data'])->toBeArray();
});
