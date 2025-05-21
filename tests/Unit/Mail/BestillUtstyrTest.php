<?php

namespace Tests\Unit\Mail;

use App\Mail\BestillUtstyr;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class BestillUtstyrTest extends TestCase
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
    public function it_has_correct_properties()
    {
        $utstyr = ['item1', 'item2'];
        $data = ['key' => 'value'];

        $mail = new BestillUtstyr($utstyr, $data);

        $this->assertEquals($utstyr, $mail->utstyr);
        $this->assertEquals($data, $mail->data);
    }

    #[Test]
    public function it_has_correct_envelope()
    {
        $mail = new BestillUtstyr([], []);
        $envelope = $mail->envelope();

        $this->assertInstanceOf(Envelope::class, $envelope);
        $this->assertEquals('Bestill Utstyr', $envelope->subject);

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
    public function it_has_correct_content()
    {
        $mail = new BestillUtstyr([], []);
        $content = $mail->content();

        $this->assertInstanceOf(Content::class, $content);
        $this->assertEquals('emails.bestill', $content->view);
    }

    #[Test]
    public function it_has_no_attachments()
    {
        $mail = new BestillUtstyr([], []);
        $attachments = $mail->attachments();

        $this->assertIsArray($attachments);
        $this->assertEmpty($attachments);
    }

    #[Test]
    public function it_renders_with_utstyr_and_data()
    {
        $utstyr = [
            (object) [
                'id' => 1,
                'navn' => 'Item 1',
                'antall' => 2,
                'artikkelnummer' => 'A12345',
            ],
        ];

        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ];

        $mail = new BestillUtstyr($utstyr, $data);

        // This test will fail if the view doesn't exist or can't be rendered with the given data
        $rendered = $mail->render();

        // We can't easily test the exact content without mocking the view,
        // but we can at least verify that it renders without errors
        $this->assertIsString($rendered);
    }
}
