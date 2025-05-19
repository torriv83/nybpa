<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Spatie\Permission\Models\Role;

class SendReminderWhenPrescriptionExpire extends Mailable
{
    use Queueable, SerializesModels;

    public $expiringPrescriptions;

    public $expiredPrescriptions;

    /**
     * Create a new message instance.
     */
    public function __construct($expiringPrescriptions, $expiredPrescriptions)
    {
        $this->expiringPrescriptions = $expiringPrescriptions;
        $this->expiredPrescriptions = $expiredPrescriptions;
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
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.prescription_expiry',
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
