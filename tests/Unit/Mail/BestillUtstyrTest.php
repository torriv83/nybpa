<?php

namespace Tests\Unit\Mail;

use App\Mail\BestillUtstyr;
use App\Models\User;
use App\Models\Utstyr;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
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
        $utstyr = new EloquentCollection([
            new Utstyr(['navn' => 'item1']),
            new Utstyr(['navn' => 'item2']),
        ]);
        $data = ['key' => 'value'];

        $mail = new BestillUtstyr($utstyr, $data);

        $this->assertEquals($utstyr, $mail->utstyr);
        $this->assertEquals($data, $mail->data);
    }

    #[Test]
    public function it_has_correct_envelope(): void
    {
        $utstyr = new EloquentCollection([
            new Utstyr(['navn' => 'item1']),
            new Utstyr(['navn' => 'item2']),
        ]);

        $data = [
            'navn' => 'John Doe',
            'epost' => 'john@example.com',
        ];

        $mail = new BestillUtstyr($utstyr, $data);
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
    public function it_has_correct_content(): void
    {

        $mail = new BestillUtstyr(new EloquentCollection, []);
        $content = $mail->content();

        $this->assertInstanceOf(Content::class, $content);
        $this->assertEquals('emails.bestill', $content->view);
    }

    #[Test]
    public function it_has_no_attachments(): void
    {
        $mail = new BestillUtstyr(new EloquentCollection, []);
        $attachments = $mail->attachments();

        $this->assertIsArray($attachments);
        $this->assertEmpty($attachments);
    }

    /**
     * @throws \ReflectionException
     */
    #[Test]
    public function it_renders_with_utstyr_and_data(): void
    {
        $utstyr = new EloquentCollection([
            new Utstyr([
                'id' => 1,
                'navn' => 'Item 1',
                'antall' => 2,
                'artikkelnummer' => 'A12345',
            ]),
        ]);

        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ];

        $mail = new BestillUtstyr($utstyr, $data);

        try {
            $rendered = $mail->render();
            $this->assertNotEmpty($rendered); // ✅ gir faktisk verdi
        } catch (\Throwable $e) {
            $this->fail('Rendering the mail threw an exception: '.$e->getMessage());
        }
    }
}
