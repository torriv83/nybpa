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
            from: new Address('tor@trivera.net', 'Tor J. Rivera'),
            replyTo: [
                new Address('tor@trivera.net', 'Tor J. Rivera'),
            ],
            subject: 'Ny melding fra Tor',
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
