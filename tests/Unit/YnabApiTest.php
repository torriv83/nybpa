<?php

use App\Models\Ynab;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Set up fake config
    Config::set('app.ynab', 'test-token');
});

it('gets a response of 200 from YNAB API', function () {
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

it('fetches data from YNAB API and saves to database', function () {
    // Fake YNAB response with sample data
    Http::fake([
        'api.ynab.com/v1/budgets/*/months' => Http::response([
            'data' => [
                'months' => [
                    [
                        'month' => '2023-01-01',
                        'activity' => -50000, // -50 after conversion
                        'income' => 100000,   // 100 after conversion
                        'budgeted' => 75000,  // 75 after conversion
                    ],
                    [
                        'month' => '2023-02-01',
                        'activity' => -60000, // -60 after conversion
                        'income' => 110000,   // 110 after conversion
                        'budgeted' => 85000,  // 85 after conversion
                    ],
                ],
            ],
        ], 200),
    ]);

    // Call the fetchData method
    Ynab::fetchData();

    // Check that data was saved to the database
    $this->assertDatabaseHas('ynabs', [
        'month' => '2023-01-01',
    ]);

    $this->assertDatabaseHas('ynabs', [
        'month' => '2023-02-01',
    ]);

    // Get the records and check the attribute conversion
    $record = Ynab::where('month', '2023-01-01')->first();
    expect($record->income)->toBe(100);
    expect($record->activity)->toBe(-50);
    expect($record->budgeted)->toBe(75);
});

it('converts attribute values by dividing by 1000', function () {
    // Create a Ynab record with raw values
    $ynab = Ynab::create([
        'month' => '2023-03-01',
        'activity' => -70000,
        'income' => 120000,
        'budgeted' => 90000,
    ]);

    // Check that the accessor methods convert the values correctly
    expect($ynab->income)->toBe(120);
    expect($ynab->activity)->toBe(-70);
    expect($ynab->budgeted)->toBe(90);
});
