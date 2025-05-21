<?php

use App\Filament\Privat\Widgets\ApotekStats;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Create necessary roles
    Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);

    // Create mock classes
    $this->dbMock = \Mockery::mock();
    $this->resepterResourceMock = \Mockery::mock();
    $this->utstyrResourceMock = \Mockery::mock();
    $this->resepterMock = \Mockery::mock();
});

afterEach(function () {
    Mockery::close();
});

it('has correct column span', function () {
    $widget = new ApotekStats;

    $reflectionClass = new ReflectionClass($widget);
    $property = $reflectionClass->getProperty('columnSpan');

    expect($property->getValue($widget))->toBe('12');
});

it('has null polling interval', function () {
    $reflectionClass = new ReflectionClass(ApotekStats::class);
    $property = $reflectionClass->getProperty('pollingInterval');

    expect($property->getValue())->toBeNull();
});
