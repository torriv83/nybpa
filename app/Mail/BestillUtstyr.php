<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Spatie\Permission\Models\Role;

class BestillUtstyr extends Mailable
{
    /**
     * @var array<string, mixed>
     */
    public array $utstyr;

    /**
     * @var array<string, mixed>
     */
    public array $data;

    /**
     * @param  array<string, mixed>  $utstyr
     * @param  array<string, mixed>  $data
     */
    public function __construct(array $utstyr, array $data)
    {
        $this->utstyr = $utstyr;
        $this->data = $data;
    }

    public function envelope(): Envelope
    {
        /** @var User|null $admin */
        $admin = Role::findByName('admin')->users->first();

        return new Envelope(
            from: new Address($admin->email, $admin->name),
            replyTo: [new Address($admin->email, $admin->name)],
            subject: 'Bestill Utstyr',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.bestill',
            with: [
                'utstyr' => $this->utstyr,
                'data' => $this->data,
            ],
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
