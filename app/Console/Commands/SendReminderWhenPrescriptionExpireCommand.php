<?php

namespace App\Console\Commands;

use App\Mail\SendReminderWhenPrescriptionExpire;
use App\Models\Resepter;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Role;

class SendReminderWhenPrescriptionExpireCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:send-prescription-reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a reminder when prescription expire';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $targetDate = now()->addMonth()->toDateString();

        /** @var \Illuminate\Database\Eloquent\Collection<int, \App\Models\Resepter> $expiringPrescriptions */
        $expiringPrescriptions = Resepter::whereDate('validTo', '=', $targetDate)->get();

        /** @var \Illuminate\Database\Eloquent\Collection<int, \App\Models\Resepter> $expiredPrescriptions */
        $expiredPrescriptions = Resepter::whereDate('validTo', '<', now())->get();

        if ($expiringPrescriptions->isEmpty() && $expiredPrescriptions->isEmpty()) {
            return;
        }

        /** @var \App\Models\User|null $adminUser */
        $adminUser = Role::findByName('admin')->users()->first();
        Mail::to($adminUser)
            ->send(new SendReminderWhenPrescriptionExpire($expiringPrescriptions, $expiredPrescriptions));
    }
}
