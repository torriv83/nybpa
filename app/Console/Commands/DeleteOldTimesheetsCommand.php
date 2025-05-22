<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DeleteOldTimesheetsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'timesheets:delete-old';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete old deleted timesheets that are unavailable';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $date = Carbon::now()->subDays(30);

        DB::table('timesheets')
            ->where('unavailable', 1)
            ->where('deleted_at', '<', $date)
            ->delete();

        $this->info('Old deleted timesheets have been removed.');
    }
}
