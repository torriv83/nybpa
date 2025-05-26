<?php

namespace App\Mail;

use App\Models\Resepter;
use App\Models\User;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Support\Collection;
use Spatie\Permission\Models\Role;

class SendReminderWhenPrescriptionExpire extends Mailable
{
    /** @var Collection<int, Resepter> */
    public Collection $expiringPrescriptions;

    /** @var Collection<int, Resepter> */
    public Collection $expiredPrescriptions;

    /**
     * @param  Collection<int, Resepter>  $expiringPrescriptions
     * @param  Collection<int, Resepter>  $expiredPrescriptions
     */
    public function __construct(Collection $expiringPrescriptions, Collection $expiredPrescriptions)
    {
        $this->expiringPrescriptions = $expiringPrescriptions;
        $this->expiredPrescriptions = $expiredPrescriptions;
    }

    public function envelope(): Envelope
    {
        /** @var User|null $admin */
        $admin = Role::findByName('admin')->users->first();

        return new Envelope(
            from: new Address($admin->email, $admin->name),
            subject: 'Resept går snart ut.',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.prescription_expiry',
            with: [
                'expiringPrescriptions' => $this->expiringPrescriptions,
                'expiredPrescriptions' => $this->expiredPrescriptions,
            ]
        );
    }

    /**
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
