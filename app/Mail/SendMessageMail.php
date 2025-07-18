<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;

class SendMessageMail extends Mailable
{
    /**
     * @var array<string, mixed>
     */
    public mixed $body;

    /**
     * @param  array<string, mixed>  $body
     */
    public function __construct($body)
    {
        $this->body = $body;
    }

    public function envelope(): Envelope
    {
        // Sjekk om admin-bruker finnes
        $admin = Role::findByName('admin')->users->first();

        // Hvis ingen admin-bruker finnes, logg en advarsel og send en standard fra-adresse
        if (! $admin) {
            Log::warning('Ingen admin-bruker funnet. Sender e-post med standard fra-adresse.');
            $admin = (object) [
                'email' => 'default@example.com',  // Standard e-postadresse
                'name' => 'Ukjent',              // Standard navn
            ];
        }

        return new Envelope(
            from   : new Address($admin->email, $admin->name),
            replyTo: [
                new Address($admin->email, $admin->name),
            ],
            subject: 'Ny melding fra '.($admin->name ?? 'Ukjent'),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.send-message',
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
