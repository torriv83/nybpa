<?php

/**
 * Created by ${USER}.
 * Date: 27.08.2023
 * Time: 07.26
 * Company: Rivera Consulting
 */

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Spatie\Permission\Models\Role;

class sendMessageMail extends Mailable
{
    use Queueable, SerializesModels;

    public mixed $body;

    public function __construct($body)
    {
        $this->body = $body;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(Role::findByName('admin')->users->first()->email, Role::findByName('admin')->users->first()->name),
            replyTo: [
                new Address(Role::findByName('admin')->users->first()->email, Role::findByName('admin')->users->first()->name),
            ],
            subject: 'Ny melding fra '.Role::findByName('admin')->users->first()->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.send-message',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
