<?php

namespace Tests\Unit\Console\Commands;

use App\Mail\TimesheetReminderEmail;
use App\Models\Timesheet;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SendTimesheetReminderEmailTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create the Assistent permission
        Permission::create(['name' => 'Assistent', 'guard_name' => 'web']);

        // Create the admin role and assign it to a user
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $adminUser = User::factory()->create([
            'email' => 'admin@example.com',
            'name' => 'Admin User',
        ]);
        $adminUser->assignRole($adminRole);

        // Fake the Mail facade
        Mail::fake();
    }

    #[Test]
    public function it_sends_emails_to_assistants_with_timesheets()
    {
        // Create an assistant with the Assistent permission
        $assistant = User::factory()->create([
            'email' => 'assistant@example.com',
        ]);
        $assistant->givePermissionTo('Assistent');

        // Create a timesheet for the current month
        $currentMonth = Carbon::now();
        Timesheet::create([
            'user_id' => $assistant->id,
            'fra_dato' => $currentMonth->format('Y-m-d'),
            'til_dato' => $currentMonth->addHours(4)->format('Y-m-d H:i:s'),
            'totalt' => 240, // 4 hours in minutes
            'unavailable' => 0,
        ]);

        // Run the command
        $this->artisan('email:send-timesheet-reminder');

        // Assert that an email was sent to the assistant
        Mail::assertSent(TimesheetReminderEmail::class, function ($mail) use ($assistant) {
            return $mail->hasTo($assistant->email);
        });
    }

    #[Test]
    public function it_does_not_send_emails_to_assistants_without_timesheets()
    {
        // Create an assistant with the Assistent permission
        $assistant = User::factory()->create([
            'email' => 'assistant-no-timesheet@example.com',
        ]);
        $assistant->givePermissionTo('Assistent');

        // No timesheets created for this assistant

        // Run the command
        $this->artisan('email:send-timesheet-reminder');

        // Assert that no email was sent to the assistant
        Mail::assertNotSent(TimesheetReminderEmail::class, function ($mail) use ($assistant) {
            return $mail->hasTo($assistant->email);
        });
    }

    #[Test]
    public function it_does_not_send_emails_for_unavailable_timesheets()
    {
        // Create an assistant with the Assistent permission
        $assistant = User::factory()->create([
            'email' => 'assistant-unavailable@example.com',
        ]);
        $assistant->givePermissionTo('Assistent');

        // Create a timesheet marked as unavailable
        $currentMonth = Carbon::now();
        Timesheet::create([
            'user_id' => $assistant->id,
            'fra_dato' => $currentMonth->format('Y-m-d'),
            'til_dato' => $currentMonth->addHours(4)->format('Y-m-d H:i:s'),
            'totalt' => 240, // 4 hours in minutes
            'unavailable' => 1, // Marked as unavailable
        ]);

        // Run the command
        $this->artisan('email:send-timesheet-reminder');

        // Assert that no email was sent to the assistant
        Mail::assertNotSent(TimesheetReminderEmail::class, function ($mail) use ($assistant) {
            return $mail->hasTo($assistant->email);
        });
    }

    #[Test]
    public function it_sends_correct_data_in_the_email()
    {
        // Create an assistant with the Assistent permission
        $assistant = User::factory()->create([
            'email' => 'assistant-data@example.com',
        ]);
        $assistant->givePermissionTo('Assistent');

        // Create a timesheet for the current month
        $currentMonth = Carbon::now();
        $timesheet = Timesheet::create([
            'user_id' => $assistant->id,
            'fra_dato' => $currentMonth->format('Y-m-d'),
            'til_dato' => $currentMonth->addHours(4)->format('Y-m-d H:i:s'),
            'totalt' => 240, // 4 hours in minutes
            'unavailable' => 0,
        ]);

        // Run the command
        $this->artisan('email:send-timesheet-reminder');

        // Assert that the email contains the correct data
        Mail::assertSent(TimesheetReminderEmail::class, function ($mail) use ($assistant, $timesheet) {
            return $mail->hasTo($assistant->email) &&
                   isset($mail->details['timesheets']) &&
                   $mail->details['timesheets']->contains($timesheet);
        });
    }
}
