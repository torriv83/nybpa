<?php

namespace Tests\Unit\Mail;

use App\Mail\WorkReminderMail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class WorkReminderMailTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_has_correct_details_property()
    {
        $details = ['key' => 'value'];
        $mail = new WorkReminderMail($details);

        $this->assertEquals($details, $mail->details);
    }

    #[Test]
    public function it_has_correct_envelope()
    {
        $mail = new WorkReminderMail([]);
        $envelope = $mail->envelope();

        $this->assertInstanceOf(Envelope::class, $envelope);
        $this->assertEquals('Work Reminder', $envelope->subject);

        $from = $envelope->from;
        $this->assertInstanceOf(Address::class, $from);
        $this->assertEquals('bpa@trivera.net', $from->address);
        $this->assertEquals('Bpa - Tor Rivera', $from->name);
    }

    #[Test]
    public function it_has_correct_content()
    {
        $mail = new WorkReminderMail([]);
        $content = $mail->content();

        $this->assertInstanceOf(Content::class, $content);
        $this->assertEquals('emails.work_reminder', $content->markdown);
    }

    #[Test]
    public function it_has_no_attachments()
    {
        $mail = new WorkReminderMail([]);
        $attachments = $mail->attachments();

        $this->assertIsArray($attachments);
        $this->assertEmpty($attachments);
    }

    #[Test]
    public function it_renders_with_details()
    {
        $details = [
            'assistent' => 'John Doe',
            'date' => '2023-12-31',
            'time' => '14:00',
        ];

        $mail = new WorkReminderMail($details);

        // This test will fail if the view doesn't exist or can't be rendered with the given data
        $rendered = $mail->render();

        // We can't easily test the exact content without mocking the view,
        // but we can at least verify that it renders without errors
        $this->assertIsString($rendered);
    }
}
