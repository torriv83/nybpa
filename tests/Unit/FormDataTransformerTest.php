<?php

use App\Constants\Timesheet;
use App\Services\DateTimeService;
use App\Transformers\FormDataTransformer;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('transforms form data for save with allDay true', function () {
    // Mock DateTimeService
    $mockedDateTimeService = Mockery::mock(DateTimeService::class);
    $mockedDateTimeService->shouldReceive('calculateTotalMinutes')
        ->andReturn(300);  // Assume the total minutes to be 300 for simplicity

    // Swap the DateTimeService instance in the container with the mock
    $this->instance(DateTimeService::class, $mockedDateTimeService);

    $inputData = [
        'allDay' => true,
        Timesheet::FRA_DATO_DATE => '2023-10-20',
        Timesheet::TIL_DATO_DATE => '2023-10-21',
        'totalt' => '5:00',
        'unavailable' => false,
    ];

    $transformedData = FormDataTransformer::transformFormDataForSave($inputData);

    // Assertions
    expect($transformedData['fra_dato'])->toBe('2023-10-20')
        ->and($transformedData['til_dato'])->toBe('2023-10-21')
        ->and($transformedData['totalt'])->toBe(300);
});

it('transforms form data for save with allDay false', function () {
    // Mock DateTimeService
    $mockedDateTimeService = Mockery::mock(DateTimeService::class);
    $mockedDateTimeService->shouldReceive('calculateTotalMinutes')
        ->andReturn(300);  // Assume the total minutes to be 300 for simplicity
    $mockedDateTimeService->shouldReceive('calculateFormattedTimeDifference')
        ->andReturn('5:00');  // Assume a 5-hour difference for simplicity

    // Swap the DateTimeService instance in the container with the mock
    $this->instance(DateTimeService::class, $mockedDateTimeService);

    $inputData = [
        'allDay' => false,
        Timesheet::FRA_DATO_TIME => '2023-10-20 08:00',
        Timesheet::TIL_DATO_TIME => '2023-10-20 13:00',
        'totalt' => '5:00',
        'unavailable' => false,
    ];

    $transformedData = FormDataTransformer::transformFormDataForSave($inputData);

    // Assertions
    expect($transformedData['fra_dato'])->toBe('2023-10-20 08:00')
        ->and($transformedData['til_dato'])->toBe('2023-10-20 13:00')
        ->and($transformedData['totalt'])->toBe(300);
});

it('transforms form data for save with totalt not provided', function () {
    // Mock DateTimeService
    $mockedDateTimeService = Mockery::mock(DateTimeService::class);
    $mockedDateTimeService->shouldReceive('calculateTotalMinutes')
        ->andReturn(300);  // Assume the total minutes to be 300 for simplicity
    $mockedDateTimeService->shouldReceive('calculateFormattedTimeDifference')
        ->andReturn('5:00');  // Assume a 5-hour difference for simplicity

    // Swap the DateTimeService instance in the container with the mock
    $this->instance(DateTimeService::class, $mockedDateTimeService);

    $inputData = [
        'allDay' => false,
        Timesheet::FRA_DATO_TIME => '2023-10-20 08:00',
        Timesheet::TIL_DATO_TIME => '2023-10-20 13:00',
        // 'totalt' is not provided
        'unavailable' => false,
    ];

    $transformedData = FormDataTransformer::transformFormDataForSave($inputData);

    // Assertions
    expect($transformedData['totalt'])->toBe(300);
});

it('transforms form data for save with unavailable true', function () {
    // Mock DateTimeService
    $mockedDateTimeService = Mockery::mock(DateTimeService::class);
    $mockedDateTimeService->shouldReceive('calculateTotalMinutes')
        ->andReturn(300);  // Assume the total minutes to be 300 for simplicity
    $mockedDateTimeService->shouldReceive('calculateFormattedTimeDifference')
        ->andReturn('5:00');  // Assume a 5-hour difference for simplicity

    // Swap the DateTimeService instance in the container with the mock
    $this->instance(DateTimeService::class, $mockedDateTimeService);

    $inputData = [
        'allDay' => false,
        Timesheet::FRA_DATO_TIME => '2023-10-20 08:00',
        Timesheet::TIL_DATO_TIME => '2023-10-20 13:00',
        'totalt' => '5:00',
        'unavailable' => true,
    ];

    $transformedData = FormDataTransformer::transformFormDataForSave($inputData);

    // Assertions
    expect($transformedData['totalt'])->toBe(0);
});
