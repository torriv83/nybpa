<?php

namespace App\Console\Commands;

use App\Mail\WorkReminderMail;
use App\Models\Timesheet;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendWorkReminderEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:send-work-reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send an email reminder an hour before work time';


    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $timesheets = Timesheet::where('fra_dato', '>=', now())->with('user')->get();

        foreach ($timesheets as $timesheet) {
            $workTime = Carbon::parse($timesheet->fra_dato);

            // Define the reminder times
            $reminderTimes = [
                '1_day_before'   => $workTime->clone()->subDay(),
                '3_hours_before' => $workTime->clone()->subHours(3),
                '1_hour_before'  => $workTime->clone()->subHour(),
            ];

            foreach ($reminderTimes as $label => $reminderTime) {
                // Check if the current time is within a 1-minute window of the reminder time
                if (now()->gte($reminderTime) && now()->lt($reminderTime->addMinutes(1))) {
                    $this->info("Sending email ($label)");
                    $details = [
                        'date'      => $timesheet->fra_dato,
                        'time'      => $timesheet->fra_dato,
                        'assistent' => $timesheet->user->name,
                    ];

                    Mail::to('tor@trivera.net')->send(new WorkReminderMail($details));
                } else {
                    $this->comment("Not time to send email ($label)");
                }
            }
        }
    }

}
