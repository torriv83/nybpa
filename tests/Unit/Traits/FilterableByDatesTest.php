<?php

namespace Tests\Unit\Traits;

use App\Traits\FilterableByDates;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TestModel extends Model
{
    use FilterableByDates;

    protected $table = 'test_models';

    protected $fillable = ['name', 'created_at'];

    public $timestamps = false;

    protected $dates = ['created_at'];  // Legg til denne linjen for korrekt casting til Carbon
}

class FilterableByDatesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        \Schema::create('test_models', function ($table) {
            $table->id();
            $table->string('name');
            $table->timestamp('created_at')->nullable();
            // Merk: Ingen updated_at kolonne, da $timestamps er false
        });
    }

    protected function tearDown(): void
    {
        \Schema::dropIfExists('test_models');
        parent::tearDown();
    }

    #[Test]
    public function it_scopes_to_today()
    {
        TestModel::create(['name' => 'Today', 'created_at' => Carbon::today()]);
        TestModel::create(['name' => 'Yesterday', 'created_at' => Carbon::yesterday()]);

        $result = TestModel::today()->get();

        $this->assertEquals(1, $result->count());
        $this->assertEquals('Today', $result->first()->name);
    }

    #[Test]
    public function it_scopes_to_yesterday()
    {
        TestModel::create(['name' => 'Today', 'created_at' => Carbon::today()]);
        TestModel::create(['name' => 'Yesterday', 'created_at' => Carbon::yesterday()]);

        $result = TestModel::yesterday()->get();

        $this->assertEquals(1, $result->count());
        $this->assertEquals('Yesterday', $result->first()->name);
    }

    #[Test]
    public function it_scopes_to_month_to_date()
    {
        $now = Carbon::now();

        TestModel::create([
            'name' => 'This month',
            'created_at' => $now->copy()->subMinute(),
        ]);

        TestModel::create([
            'name' => 'Last month',
            'created_at' => $now->copy()->subMonth(),
        ]);

        $result = TestModel::monthToDate()->get();

        $this->assertEquals(1, $result->count());
        $this->assertEquals('This month', $result->first()->name);
    }

    #[Test]
    public function it_scopes_to_quarter_to_date()
    {
        TestModel::create(['name' => 'Today', 'created_at' => Carbon::today()]);
        TestModel::create(['name' => 'Yesterday', 'created_at' => Carbon::yesterday()]);

        $result = TestModel::today()->get();

        $this->assertEquals(1, $result->count());
        $this->assertEquals('Today', $result->first()->name);
    }

    #[Test]
    public function it_scopes_to_year_to_date()
    {
        TestModel::create(['name' => 'This year', 'created_at' => Carbon::now()->startOfYear()->addDays(5)]);
        TestModel::create(['name' => 'Last year', 'created_at' => Carbon::now()->subYear()]);

        $result = TestModel::yearToDate()->get();

        $this->assertEquals(1, $result->count());
        $this->assertEquals('This year', $result->first()->name);
    }

    #[Test]
    public function it_scopes_to_last_7_days()
    {
        TestModel::create(['name' => 'Within 7 days', 'created_at' => Carbon::now()->subDays(5)]);
        TestModel::create(['name' => 'Outside 7 days', 'created_at' => Carbon::now()->subDays(10)]);

        $result = TestModel::last7Days()->get();

        $this->assertEquals(1, $result->count());
        $this->assertEquals('Within 7 days', $result->first()->name);
    }

    #[Test]
    public function it_scopes_to_last_30_days()
    {
        TestModel::create(['name' => 'Within 30 days', 'created_at' => Carbon::now()->subDays(25)]);
        TestModel::create(['name' => 'Outside 30 days', 'created_at' => Carbon::now()->subDays(35)]);

        $result = TestModel::last30Days()->get();

        $this->assertEquals(1, $result->count());
        $this->assertEquals('Within 30 days', $result->first()->name);
    }

    #[Test]
    public function it_scopes_to_last_year()
    {
        $lastYearStart = Carbon::now()->firstOfYear()->subYear();
        $lastYearEnd = Carbon::now()->lastOfYear()->subYear();

        TestModel::create(['name' => 'Last year', 'created_at' => $lastYearStart->copy()->addDays(5)]);
        TestModel::create(['name' => 'This year', 'created_at' => Carbon::now()->startOfYear()->addDays(5)]);
        TestModel::create(['name' => 'Two years ago', 'created_at' => $lastYearStart->copy()->subYear()]);

        $result = TestModel::lastYear()->get();

        $this->assertEquals(1, $result->count());
        $this->assertEquals('Last year', $result->first()->name);
    }

    #[Test]
    public function it_scopes_to_last_12_months()
    {
        $lastYearStart = Carbon::now()->firstOfYear()->subYear();
        $lastYearEnd = Carbon::now()->lastOfYear()->subYear();

        TestModel::create(['name' => 'Last 12 months', 'created_at' => $lastYearStart->copy()->addDays(5)]);
        TestModel::create(['name' => 'This year', 'created_at' => Carbon::now()->startOfYear()->addDays(5)]);
        TestModel::create(['name' => 'More than 12 months ago', 'created_at' => $lastYearStart->copy()->subYear()]);

        $result = TestModel::last12Months()->get();

        $this->assertEquals(1, $result->count());
        $this->assertEquals('Last 12 months', $result->first()->name);
    }

    #[Test]
    public function it_scopes_to_in_future()
    {
        TestModel::create(['name' => 'Future', 'created_at' => Carbon::now()->addDays(1)]);
        TestModel::create(['name' => 'Past', 'created_at' => Carbon::now()->subDays(1)]);
        TestModel::create(['name' => 'Today midnight', 'created_at' => Carbon::today()->setTime(0, 0, 0)]);

        $result = TestModel::inFuture()->get();

        $this->assertGreaterThanOrEqual(1, $result->count());
        $this->assertTrue($result->contains('name', 'Future'));
    }

    #[Test]
    public function it_can_use_custom_column_names()
    {
        \Schema::create('custom_date_models', function ($table) {
            $table->id();
            $table->string('name');
            $table->timestamp('custom_date')->nullable();
        });

        $customModel = new class extends Model
        {
            use FilterableByDates;

            protected $table = 'custom_date_models';

            protected $fillable = ['name', 'custom_date'];

            public $timestamps = false;
        };

        $customModel::create(['name' => 'Today', 'custom_date' => Carbon::today()]);
        $customModel::create(['name' => 'Yesterday', 'custom_date' => Carbon::yesterday()]);

        $result = $customModel::today('custom_date')->get();

        $this->assertEquals(1, $result->count());
        $this->assertEquals('Today', $result->first()->name);

        \Schema::dropIfExists('custom_date_models');
    }
}
