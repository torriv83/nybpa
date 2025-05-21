<?php

use App\Models\Settings;
use App\Models\Timesheet;
use App\Models\User;
use App\Services\UserStatsService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Mock Cache
    Cache::shouldReceive('tags')->andReturnSelf();
    Cache::shouldReceive('remember')->andReturnUsing(fn ($key, $ttl, $callback) => $callback());

    // Testbruker
    $this->user = User::factory()->create([
        'email' => 'test@example.com',
        'name' => 'Test User',
    ]);

    // Registrer mocket Settings-instans i containeren
    $mockSettings = Mockery::mock(Settings::class);
    $mockSettings->shouldReceive('getUserBpa')->andReturn(40);
    App::instance(Settings::class, $mockSettings);

    // Lag tjenesten
    $this->service = new UserStatsService;
});

it('gets the number of assistants', function () {
    $tilkalling = Role::firstOrCreate(['name' => 'Tilkalling']);
    $fastAnsatt = Role::firstOrCreate(['name' => 'Fast ansatt']);

    User::factory()->create()->assignRole($tilkalling);
    User::factory()->create()->assignRole($fastAnsatt);

    // Denne skal ikke telles
    $admin = Role::firstOrCreate(['name' => 'Admin']);
    User::factory()->create()->assignRole($admin);

    $result = $this->service->getNumberOfAssistents();

    expect($result)->toBe(2);
});

it('calculates remaining hours correctly', function () {
    $startOfYear = Carbon::now()->startOfYear();

    Timesheet::create([
        'user_id' => $this->user->id,
        'fra_dato' => $startOfYear->copy()->addDay(),
        'til_dato' => $startOfYear->copy()->addDay()->addHours(50),
        'totalt' => 3000,
        'unavailable' => 0,
    ]);

    Timesheet::create([
        'user_id' => $this->user->id,
        'fra_dato' => $startOfYear->copy()->addDays(10),
        'til_dato' => $startOfYear->copy()->addDays(10)->addHours(33),
        'totalt' => 2000,
        'unavailable' => 0,
    ]);

    $reflection = new ReflectionClass($this->service);
    $property = $reflection->getProperty('bpa');
    $property->setAccessible(true);
    $property->setValue($this->service, 40);

    $result = $this->service->getRemainingHours();

    expect($result)->toBe('1996:40');
});

it('gets hours used this week', function () {
    $startOfWeek = Carbon::now()->startOfWeek();

    Timesheet::create([
        'user_id' => $this->user->id,
        'fra_dato' => $startOfWeek->copy()->addDay(),
        'til_dato' => $startOfWeek->copy()->addDay()->addHours(4),
        'totalt' => 240,
        'unavailable' => 0,
    ]);

    Timesheet::create([
        'user_id' => $this->user->id,
        'fra_dato' => $startOfWeek->copy()->addDays(2),
        'til_dato' => $startOfWeek->copy()->addDays(2)->addHours(6),
        'totalt' => 360,
        'unavailable' => 0,
    ]);

    Timesheet::create([
        'user_id' => $this->user->id,
        'fra_dato' => $startOfWeek->copy()->addDays(3),
        'til_dato' => $startOfWeek->copy()->addDays(3)->addHours(8),
        'totalt' => 480,
        'unavailable' => 1,
    ]);

    $result = $this->service->getHoursUsedThisWeek();

    expect($result)->toBe('10:00');
});

it('gets yearly time chart data', function () {
    // Mock the timesheet->timeUsedThisYear method
    $mockTimesheet = Mockery::mock(Timesheet::class);
    $mockTimesheet->shouldReceive('timeUsedThisYear')->andReturn(collect([
        1 => [
            (object) ['totalt' => 2400], // 40 hours
        ],
        2 => [
            (object) ['totalt' => 1800], // 30 hours
        ],
    ]));

    // Use reflection to set the protected timesheet property
    $reflectionClass = new ReflectionClass(UserStatsService::class);
    $reflectionProperty = $reflectionClass->getProperty('timesheet');
    $reflectionProperty->setAccessible(true);
    $reflectionProperty->setValue($this->service, $mockTimesheet);

    // 👇 Husk å sette bpa = 40
    $reflection = new ReflectionClass($this->service);
    $property = $reflection->getProperty('bpa');
    $property->setAccessible(true);
    $property->setValue($this->service, 40);

    $result = $this->service->getYearlyTimeChart();

    expect($result)->toEqual([
        1 => 6000.0,
        2 => 10500.0,
    ]);
});
