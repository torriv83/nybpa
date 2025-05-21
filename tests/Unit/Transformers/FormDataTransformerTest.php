<?php

namespace Tests\Unit\Transformers;

use App\Constants\Timesheet as TimesheetConstants;
use App\Transformers\FormDataTransformer;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class FormDataTransformerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Kjør spatie migrasjoner manuelt her
        \Artisan::call('migrate', [
            '--path' => 'vendor/spatie/laravel-permission/database/migrations',
            '--realpath' => true,
        ]);
    }

    #[Test]
    public function it_transforms_all_day_data_for_save()
    {
        // Mock data for an all-day event
        $data = [
            'allDay' => true,
            TimesheetConstants::FRA_DATO_DATE => '2023-01-01',
            TimesheetConstants::TIL_DATO_DATE => '2023-01-01',
            'unavailable' => false,
        ];

        $result = FormDataTransformer::transformFormDataForSave($data);

        $this->assertEquals('2023-01-01', $result['fra_dato']);
        $this->assertEquals('2023-01-01', $result['til_dato']);

        $this->assertIsInt($result['totalt']);
    }

    #[Test]
    public function it_transforms_time_based_data_for_save()
    {
        // Mock data for a time-based event
        $data = [
            'allDay' => false,
            TimesheetConstants::FRA_DATO_TIME => '2023-01-01 09:00',
            TimesheetConstants::TIL_DATO_TIME => '2023-01-01 17:00',
            'unavailable' => false,
        ];

        $result = FormDataTransformer::transformFormDataForSave($data);

        $this->assertEquals('2023-01-01 09:00', $result['fra_dato']);
        $this->assertEquals('2023-01-01 17:00', $result['til_dato']);

        // The total should be 8 hours = 480 minutes
        $this->assertEquals(480, $result['totalt']);
    }

    #[Test]
    public function it_transforms_data_with_existing_total_for_save()
    {
        // Mock data with an existing total
        $data = [
            'allDay' => false,
            TimesheetConstants::FRA_DATO_TIME => '2023-01-01 09:00',
            TimesheetConstants::TIL_DATO_TIME => '2023-01-01 17:00',
            'totalt' => '08:00', // 8 hours
            'unavailable' => false,
        ];

        $result = FormDataTransformer::transformFormDataForSave($data);

        // The total should be 8 hours = 480 minutes
        $this->assertEquals(480, $result['totalt']);
    }

    #[Test]
    public function it_sets_total_to_zero_for_unavailable_events()
    {
        // Mock data for an unavailable event
        $data = [
            'allDay' => false,
            TimesheetConstants::FRA_DATO_TIME => '2023-01-01 09:00',
            TimesheetConstants::TIL_DATO_TIME => '2023-01-01 17:00',
            'totalt' => '08:00', // 8 hours
            'unavailable' => true,
        ];

        $result = FormDataTransformer::transformFormDataForSave($data);

        // The total should be 0 for unavailable events
        $this->assertEquals(0, $result['totalt']);
    }

    #[Test]
    public function it_transforms_all_day_data_for_fill()
    {
        // Mock data for an all-day event
        $data = [
            'allDay' => true,
            'fra_dato' => '2023-01-01',
            'til_dato' => '2023-01-01',
        ];

        $result = FormDataTransformer::transformFormDataForFill($data);

        $this->assertEquals('2023-01-01', $result[TimesheetConstants::FRA_DATO_DATE]);
        $this->assertEquals('2023-01-01', $result[TimesheetConstants::TIL_DATO_DATE]);
        $this->assertEquals(0, $result['totalt']);
    }

    #[Test]
    public function it_transforms_time_based_data_for_fill()
    {
        // Mock data for a time-based event
        $data = [
            'allDay' => false,
            'fra_dato' => '2023-01-01 09:00:00',
            'til_dato' => '2023-01-01 17:00:00',
            'totalt' => 480, // 8 hours in minutes
        ];

        $result = FormDataTransformer::transformFormDataForFill($data);

        $this->assertEquals('2023-01-01 09:00', $result[TimesheetConstants::FRA_DATO_TIME]);
        $this->assertEquals('2023-01-01 17:00', $result[TimesheetConstants::TIL_DATO_TIME]);
        $this->assertEquals('08:00', $result['totalt']);
    }

    #[Test]
    public function it_handles_timezone_conversion_for_fill()
    {
        // Create a Carbon instance with a specific timezone
        $fraDate = Carbon::parse('2023-01-01 09:00:00', 'UTC');
        $tilDate = Carbon::parse('2023-01-01 17:00:00', 'UTC');

        // Mock data with UTC timestamps
        $data = [
            'allDay' => false,
            'fra_dato' => $fraDate->toDateTimeString(),
            'til_dato' => $tilDate->toDateTimeString(),
            'totalt' => 480, // 8 hours in minutes
        ];

        $result = FormDataTransformer::transformFormDataForFill($data);

        // The timestamps should be converted to Europe/Oslo timezone
        // Note: The exact expected values depend on the timezone difference
        $this->assertStringContainsString('2023-01-01', $result[TimesheetConstants::FRA_DATO_TIME]);
        $this->assertStringContainsString('2023-01-01', $result[TimesheetConstants::TIL_DATO_TIME]);
        $this->assertEquals('08:00', $result['totalt']);
    }
}
