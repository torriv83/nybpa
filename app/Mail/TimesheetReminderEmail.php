<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Spatie\Permission\Models\Role;

class TimesheetReminderEmail extends Mailable
{
    /**
     * @var array<string, mixed>|null
     */
    public ?array $details;

    /**
     * @param  array{timesheets: \Illuminate\Database\Eloquent\Collection<int, \App\Models\Timesheet>}  $details
     */
    public function __construct(array $details)
    {
        $this->details = $details;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        /** @var User|null $admin */
        $admin = Role::findByName('admin')->users->first();
        $email = $admin ? $admin->email : '';
        $name = $admin ? $admin->name : '';

        return new Envelope(
            from   : new Address($email, $name),
            subject: 'Oversikt over timer jobbet',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.timesheet_reminder',
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
