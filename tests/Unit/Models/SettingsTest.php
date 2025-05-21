<?php

namespace Tests\Unit\Models;

use App\Models\Settings;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SettingsTest extends TestCase
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

        $settings = Settings::create([
            'user_id' => $user->id,
            'bpa_hours_per_week' => 40,
            'weekplan_timespan' => true,
            'weekplan_from' => now(),
            'weekplan_to' => now()->addWeek(),
            'apotek_epost' => 'test@example.com',
        ]);

        $this->assertInstanceOf(User::class, $settings->user);
        $this->assertEquals($user->id, $settings->user->id);
    }

    #[Test]
    public function it_returns_default_user_when_user_is_deleted()
    {
        $user = User::factory()->create();

        $settings = Settings::create([
            'user_id' => $user->id,
            'bpa_hours_per_week' => 40,
            'weekplan_timespan' => true,
            'weekplan_from' => now(),
            'weekplan_to' => now()->addWeek(),
            'apotek_epost' => 'test@example.com',
        ]);

        // Delete the user
        $user->delete();

        // Force a fresh retrieval of the settings
        $settings = Settings::find($settings->id);

        // The user should be the default "Tidligere ansatt"
        $this->assertEquals('Tidligere ansatt', $settings->user->name);
    }

    #[Test]
    public function it_gets_user_bpa()
    {
        $user = User::factory()->create();

        // Create settings for the user
        Settings::create([
            'user_id' => $user->id,
            'bpa_hours_per_week' => 40,
            'weekplan_timespan' => true,
            'weekplan_from' => now(),
            'weekplan_to' => now()->addWeek(),
            'apotek_epost' => 'test@example.com',
        ]);

        // Mock Auth to return the user's ID
        Auth::shouldReceive('id')->andReturn($user->id);

        $result = Settings::getUserBpa();

        $this->assertEquals(40, $result);
    }

    #[Test]
    public function it_returns_default_bpa_when_no_settings_exist()
    {
        // Mock Auth to return a non-existent user ID
        Auth::shouldReceive('id')->andReturn(999);

        $result = Settings::getUserBpa();

        // Should return the default value of 1
        $this->assertEquals(1, $result);
    }

    #[Test]
    public function it_gets_user_apotek_epost()
    {
        $user = User::factory()->create();

        // Create settings for the user
        Settings::create([
            'user_id' => $user->id,
            'bpa_hours_per_week' => 40,
            'weekplan_timespan' => true,
            'weekplan_from' => now(),
            'weekplan_to' => now()->addWeek(),
            'apotek_epost' => 'test@example.com',
        ]);

        // Mock Auth to return the user's ID
        Auth::shouldReceive('id')->andReturn($user->id);

        $result = Settings::getUserApotekEpost();

        $this->assertEquals('test@example.com', $result);
    }

    #[Test]
    public function it_gets_user_settings()
    {
        $user = User::factory()->create();

        // Create settings for the user
        $settings = Settings::create([
            'user_id' => $user->id,
            'bpa_hours_per_week' => 40,
            'weekplan_timespan' => true,
            'weekplan_from' => now(),
            'weekplan_to' => now()->addWeek(),
            'apotek_epost' => 'test@example.com',
        ]);

        $result = Settings::getUserSettings($user->id);

        $this->assertInstanceOf(Settings::class, $result);
        $this->assertEquals($settings->id, $result->id);
    }

    #[Test]
    public function it_casts_weekplan_timespan_to_boolean()
    {
        $settings = Settings::create([
            'user_id' => 1,
            'bpa_hours_per_week' => 40,
            'weekplan_timespan' => 1, // Integer value
            'weekplan_from' => now(),
            'weekplan_to' => now()->addWeek(),
            'apotek_epost' => 'test@example.com',
        ]);

        // Retrieve the settings from the database
        $settings = Settings::find($settings->id);

        // The weekplan_timespan should be cast to a boolean
        $this->assertIsBool($settings->weekplan_timespan);
        $this->assertTrue($settings->weekplan_timespan);
    }
}
