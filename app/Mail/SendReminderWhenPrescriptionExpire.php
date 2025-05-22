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
    public Collection $expiringPrescriptions;

    public Collection $expiredPrescriptions;

    /**
     * Create a new message instance.
     */
    public function __construct($expiringPrescriptions, $expiredPrescriptions)
    {
        // Konverterer array til objekt for konsistens
        $this->expiringPrescriptions = collect($expiringPrescriptions)->map(function ($prescription) {
            return is_array($prescription) ? (object) $prescription : $prescription;
        });

        $this->expiredPrescriptions = collect($expiredPrescriptions)->map(function ($prescription) {
            return is_array($prescription) ? (object) $prescription : $prescription;
        });
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        /** @var User|null $admin */
        $admin = Role::findByName('admin')->users->first();

        return new Envelope(
            from   : new Address($admin->email, $admin->name),
            subject: 'Resept går snart ut.',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content()
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
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
