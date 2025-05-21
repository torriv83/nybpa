<?php

namespace Tests\Unit\Mail;

use App\Mail\sendMessageMail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SendMessageMailTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create an admin role and user
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $adminUser = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
        ]);
        $adminUser->assignRole($adminRole);
    }

    #[Test]
    public function it_has_correct_body_property()
    {
        $body = 'Test message body';
        $mail = new sendMessageMail($body);

        $this->assertEquals($body, $mail->body);
    }

    #[Test]
    public function it_has_correct_envelope()
    {
        $mail = new sendMessageMail('Test message');
        $envelope = $mail->envelope();

        $this->assertInstanceOf(Envelope::class, $envelope);
        $this->assertStringContainsString('Ny melding fra', $envelope->subject);

        $from = $envelope->from;
        $this->assertInstanceOf(Address::class, $from);
        $this->assertEquals('admin@example.com', $from->address);
        $this->assertEquals('Admin User', $from->name);

        $replyTo = $envelope->replyTo;
        $this->assertCount(1, $replyTo);
        $this->assertEquals('admin@example.com', $replyTo[0]->address);
        $this->assertEquals('Admin User', $replyTo[0]->name);
    }

    #[Test]
    public function it_handles_missing_admin_user()
    {
        // Delete all admin users
        User::whereHas('roles', function ($query) {
            $query->where('name', 'admin');
        })->delete();

        // Clear role cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $mail = new sendMessageMail('Test message');
        $envelope = $mail->envelope();

        $this->assertInstanceOf(Envelope::class, $envelope);
        $this->assertEquals('Ny melding fra Ukjent', $envelope->subject);
    }

    #[Test]
    public function it_has_correct_content()
    {
        $mail = new sendMessageMail('Test message');
        $content = $mail->content();

        $this->assertInstanceOf(Content::class, $content);
        $this->assertEquals('emails.send-message', $content->markdown);
    }

    #[Test]
    public function it_has_no_attachments()
    {
        $mail = new sendMessageMail('Test message');
        $attachments = $mail->attachments();

        $this->assertIsArray($attachments);
        $this->assertEmpty($attachments);
    }

    #[Test]
    public function it_renders_with_body()
    {
        $body = 'This is a test message body';
        $mail = new sendMessageMail($body);

        // This test will fail if the view doesn't exist or can't be rendered with the given data
        $rendered = $mail->render();

        // We can't easily test the exact content without mocking the view,
        // but we can at least verify that it renders without errors
        $this->assertIsString($rendered);
    }
}
