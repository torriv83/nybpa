<?php

use Illuminate\Support\Facades\Http;

it('get a response of 200', function () {
// Arrange - Set up the necessary data for the test
    $ynab  = 'https://api.youneedabudget.com/v1/budgets/d7e4da92-0564-4e8f-87f5-c491ca545435/';
    $token = config('app.ynab');

// Act - Perform the HTTP request and get the response
    $response = Http::withToken($token)->get($ynab . 'accounts/');

// Assert - Check if the response status code is 200
    expect($response['data'])->toBeArray();
});
