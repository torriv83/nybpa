<?php

namespace App\Mail;

use App\Models\Utstyr;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Support\Facades\Session;
use Illuminate\Contracts\Queue\ShouldQueue;

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
     *
     * @return Envelope
     */
    public function envelope() : Envelope
    {
        return new Envelope(
            from: new Address('tor@trivera.net', 'Tor J. Rivera'),
            replyTo: [
                new Address('tor@trivera.net', 'Tor J. Rivera'),
            ],
            subject: 'Bestill Utstyr',
        );
    }

    /**
     * Get the message content definition.
     *
     * @return Content
     */
    public function content() : Content
    {
        return new Content(
            view: 'emails.bestill',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments() : array
    {
        return [];
    }
}
