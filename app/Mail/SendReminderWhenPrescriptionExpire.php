<?php

namespace App\Mail;

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
    /** @var Collection<int, object> */
    public Collection $expiringPrescriptions;

    /** @var Collection<int, object> */
    public Collection $expiredPrescriptions;

    /**
     * @param  array<int, array<string, mixed>|object>  $expiringPrescriptions
     * @param  array<int, array<string, mixed>|object>  $expiredPrescriptions
     */
    public function __construct(array $expiringPrescriptions, array $expiredPrescriptions)
    {
        $this->expiringPrescriptions = collect($expiringPrescriptions)->map(
            fn ($prescription): object => is_array($prescription) ? (object) $prescription : $prescription
        );

        $this->expiredPrescriptions = collect($expiredPrescriptions)->map(
            fn ($prescription): object => is_array($prescription) ? (object) $prescription : $prescription
        );
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
