<?php

use App\Models\Timesheet;
use App\Services\DateTimeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;

uses(RefreshDatabase::class);

it('calculates total minutes correctly', function () {
    $time = '2:30';
    $minutes = DateTimeService::calculateTotalMinutes($time);
    expect($minutes)->toBe(150);
});

it('returns zero for invalid input', function () {
    $time = 'invalid input';
    $minutes = DateTimeService::calculateTotalMinutes($time);
    expect($minutes)->toBe(0);
});

it('calculates formatted time difference correctly', function () {
    $startTime = Carbon::parse('2023-10-20 08:00:00');
    $endTime = Carbon::parse('2023-10-20 10:30:00');
    $formattedTimeDifference = DateTimeService::calculateFormattedTimeDifference($startTime, $endTime);
    expect($formattedTimeDifference)->toBe('02:30');
});

it('retrieves all disabled dates correctly', function () {

    $date = Carbon::now()->addWeek()->format('Y-m-d');

    Timesheet::factory()->create([
        'fra_dato' => $date,
        'user_id' => 1,
    ]);

    $disabledDates = DateTimeService::getAllDisabledDates(1, null);
    expect($disabledDates)->toBe([$date]);
});
