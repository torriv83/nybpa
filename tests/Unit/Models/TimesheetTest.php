<?php

namespace Tests\Unit\Models;

use App\Models\Timesheet;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TimesheetTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Mock Cache
        Cache::shouldReceive('tags')->andReturnSelf();
        Cache::shouldReceive('remember')->andReturnUsing(fn ($key, $ttl, $callback) => $callback());
    }

    #[Test]
    public function it_belongs_to_a_user()
    {
        $user = User::factory()->create();

        $timesheet = Timesheet::create([
            'user_id' => $user->id,
            'fra_dato' => now(),
            'til_dato' => now()->addHours(4),
            'totalt' => 240, // 4 hours in minutes
        ]);

        $this->assertInstanceOf(User::class, $timesheet->user);
        $this->assertEquals($user->id, $timesheet->user->id);
    }

    #[Test]
    public function it_returns_default_user_when_user_is_deleted()
    {
        $user = User::factory()->create();

        $timesheet = Timesheet::create([
            'user_id' => $user->id,
            'fra_dato' => now(),
            'til_dato' => now()->addHours(4),
            'totalt' => 240,
        ]);

        // Delete the user
        $user->delete();

        // Force a fresh retrieval of the timesheet
        $timesheet = Timesheet::find($timesheet->id);

        // The user should be the default "Tidligere ansatt"
        $this->assertEquals('Tidligere ansatt', $timesheet->user->name);
    }

    #[Test]
    public function it_scopes_to_this_year()
    {
        $user = \App\Models\User::factory()->create();

        // Create a timesheet for this year
        Timesheet::create([
            'user_id' => $user->id,
            'fra_dato' => Carbon::now()->startOfYear(),
            'til_dato' => Carbon::now()->startOfYear()->addHours(4),
            'totalt' => 240,
        ]);

        // Create a timesheet for last year
        Timesheet::create([
            'user_id' => $user->id,
            'fra_dato' => Carbon::now()->subYear()->startOfYear(),
            'til_dato' => Carbon::now()->subYear()->startOfYear()->addHours(4),
            'totalt' => 240,
        ]);

        $timesheets = Timesheet::thisYear()->get();

        $this->assertEquals(1, $timesheets->count());
        $this->assertEquals(Carbon::now()->year, $timesheets->first()->til_dato->year);
    }

    #[Test]
    public function it_scopes_to_this_month()
    {
        $user = \App\Models\User::factory()->create();

        // Create a timesheet for this month
        Timesheet::create([
            'user_id' => $user->id,
            'fra_dato' => Carbon::now()->startOfMonth(),
            'til_dato' => Carbon::now()->startOfMonth()->addHours(4),
            'totalt' => 240,
        ]);

        // Create a timesheet for last month
        Timesheet::create([
            'user_id' => $user->id,
            'fra_dato' => Carbon::now()->subMonth()->startOfMonth(),
            'til_dato' => Carbon::now()->subMonth()->startOfMonth()->addHours(4),
            'totalt' => 240,
        ]);

        $timesheets = Timesheet::thisMonth()->get();

        $this->assertEquals(1, $timesheets->count());
        $this->assertEquals(Carbon::now()->month, $timesheets->first()->til_dato->month);
    }

    #[Test]
    public function it_gets_time_used_this_year()
    {
        $user = \App\Models\User::factory()->create();

        // Create some timesheets for this year
        Timesheet::create([
            'user_id' => $user->id,
            'fra_dato' => Carbon::now()->startOfYear(),
            'til_dato' => Carbon::now()->startOfYear()->addHours(4),
            'totalt' => 240,
            'unavailable' => 0,
        ]);

        Timesheet::create([
            'user_id' => $user->id,
            'fra_dato' => Carbon::now()->startOfYear()->addMonth(),
            'til_dato' => Carbon::now()->startOfYear()->addMonth()->addHours(4),
            'totalt' => 240,
            'unavailable' => 0,
        ]);

        // Create a timesheet that should be excluded (unavailable)
        Timesheet::create([
            'user_id' => $user->id,
            'fra_dato' => Carbon::now()->startOfYear()->addMonths(2),
            'til_dato' => Carbon::now()->startOfYear()->addMonths(2)->addHours(4),
            'totalt' => 240,
            'unavailable' => 1,
        ]);

        $timesheet = new Timesheet;
        $result = $timesheet->timeUsedThisYear();

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertEquals(2, $result->flatten()->count());
    }

    #[Test]
    public function it_gets_time_used_last_year()
    {
        $user = \App\Models\User::factory()->create();
        // Create some timesheets for last year
        Timesheet::create([
            'user_id' => $user->id,
            'fra_dato' => Carbon::now()->subYear()->startOfYear(),
            'til_dato' => Carbon::now()->subYear()->startOfYear()->addHours(4),
            'totalt' => 240,
            'unavailable' => 0,
        ]);

        Timesheet::create([
            'user_id' => $user->id,
            'fra_dato' => Carbon::now()->subYear()->startOfYear()->addMonth(),
            'til_dato' => Carbon::now()->subYear()->startOfYear()->addMonth()->addHours(4),
            'totalt' => 240,
            'unavailable' => 0,
        ]);

        // Create a timesheet that should be excluded (unavailable)
        Timesheet::create([
            'user_id' => $user->id,
            'fra_dato' => Carbon::now()->subYear()->startOfYear()->addMonths(2),
            'til_dato' => Carbon::now()->subYear()->startOfYear()->addMonths(2)->addHours(4),
            'totalt' => 240,
            'unavailable' => 1,
        ]);

        $timesheet = new Timesheet;
        $result = $timesheet->timeUsedLastYear();

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertEquals(2, $result->flatten()->count());
    }

    #[Test]
    public function it_scopes_to_disabled_dates()
    {
        $user = User::factory()->create();

        // Create some timesheets for this year
        $timesheet1 = Timesheet::create([
            'user_id' => $user->id,
            'fra_dato' => Carbon::now()->startOfYear(),
            'til_dato' => Carbon::now()->startOfYear()->addHours(4),
            'totalt' => 240,
        ]);

        $timesheet2 = Timesheet::create([
            'user_id' => $user->id,
            'fra_dato' => Carbon::now()->startOfYear()->addMonth(),
            'til_dato' => Carbon::now()->startOfYear()->addMonth()->addHours(4),
            'totalt' => 240,
        ]);

        // Create a timesheet for a different user
        $otherUser = User::factory()->create();
        Timesheet::create([
            'user_id' => $otherUser->id,
            'fra_dato' => Carbon::now()->startOfYear(),
            'til_dato' => Carbon::now()->startOfYear()->addHours(4),
            'totalt' => 240,
        ]);

        // Test without excluding any record
        $result1 = Timesheet::disabledDates($user->id, null)->get();
        $this->assertEquals(2, $result1->count());

        // Test with excluding a specific record
        $result2 = Timesheet::disabledDates($user->id, $timesheet1->id)->get();
        $this->assertEquals(1, $result2->count());
        $this->assertEquals($timesheet2->id, $result2->first()->id);
    }

    #[Test]
    public function it_uses_soft_deletes()
    {
        $user = \App\Models\User::factory()->create();
        $timesheet = Timesheet::create([
            'user_id' => $user->id,
            'fra_dato' => now(),
            'til_dato' => now()->addHours(4),
            'totalt' => 240,
        ]);

        $timesheetId = $timesheet->id;

        // Delete the timesheet
        $timesheet->delete();

        // The timesheet should still exist in the database
        $this->assertDatabaseHas('timesheets', ['id' => $timesheetId]);

        // But should not be retrieved in a normal query
        $this->assertNull(Timesheet::find($timesheetId));

        // Should be retrieved when including trashed
        $this->assertNotNull(Timesheet::withTrashed()->find($timesheetId));
    }
}
