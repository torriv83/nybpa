<?php

namespace App\Console;

use App\Models\Ynab;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        Commands\SendWorkReminderEmailCommand::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @return void
     */
    protected function schedule(Schedule $schedule)
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
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
