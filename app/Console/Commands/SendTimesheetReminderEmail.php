<?php

namespace App\Console\Commands;

use App\Mail\TimesheetReminderEmail;
use App\Models\Timesheet;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendTimesheetReminderEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:send-timesheet-reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a reminder to all assistants to send in their timesheets';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Get the current month
        $currentMonth = now()->format('m');
        $currentYear = now()->format('Y');

        // Get all assistants (update with your logic to retrieve assistants)
        $assistants = User::permission('Assistent')->get();

        // Iterate through assistants and send email
        foreach ($assistants as $assistant) {
            // Retrieve worked times for the assistant for the current month
            $timesheets = Timesheet::where('user_id', $assistant->id)
                ->where('unavailable', '=', 0)
                ->whereNull('deleted_at')
                ->whereMonth('fra_dato', $currentMonth)
                ->whereYear('fra_dato', $currentYear)
                ->orderBy('fra_dato')
                ->get();

            // If the assistant has not worked this month, skip to the next assistant
            if ($timesheets->isEmpty()) {
                continue; // Skip to the next iteration of the loop
            }

            // Prepare email details (e.g., worked times)
            $details = [
                'timesheets' => $timesheets,
            ];

            // Send email (update with your mailable class)
            Mail::to($assistant->email)->send(new TimesheetReminderEmail($details));

        }
    }
}
