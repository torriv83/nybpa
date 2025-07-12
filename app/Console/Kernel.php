<?php

namespace App\Console;

use App\Models\Ynab;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * @var array<int, class-string<\Illuminate\Console\Command>>
     */
    protected $commands = [
        Commands\SendWorkReminderEmailCommand::class,
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('email:send-work-reminder')->everyMinute();
        $schedule->command('timesheets:delete-old')->daily();
        $schedule->command('email:send-timesheet-reminder')->lastDayOfMonth('12:00');
        $schedule->call(function () {
            Ynab::fetchData();
        })->everySixHours();
        $schedule->command('email:send-prescription-reminder')->daily();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
