<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Spatie\Permission\Models\Role;

class BestillUtstyr extends Mailable
{
    use Queueable, SerializesModels;

    public $utstyr;

    public $data;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($utstyr, $data)
    {
        $this->utstyr = $utstyr;
        $this->data = $data;
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
            replyTo: [
                new Address($admin->email, $admin->name),
            ],
            subject: 'Bestill Utstyr',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.bestill',
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
