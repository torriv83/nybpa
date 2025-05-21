<?php

namespace Tests\Unit\Mail;

use App\Mail\SendReminderWhenPrescriptionExpire;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SendReminderWhenPrescriptionExpireTest extends TestCase
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
        $expiringPrescriptions = collect(['prescription1', 'prescription2']);
        $expiredPrescriptions = collect(['prescription3', 'prescription4']);

        $mail = new SendReminderWhenPrescriptionExpire($expiringPrescriptions, $expiredPrescriptions);

        $this->assertEquals($expiringPrescriptions, $mail->expiringPrescriptions);
        $this->assertEquals($expiredPrescriptions, $mail->expiredPrescriptions);
    }

    #[Test]
    public function it_has_correct_envelope()
    {
        $mail = new SendReminderWhenPrescriptionExpire([], []);
        $envelope = $mail->envelope();

        $this->assertInstanceOf(Envelope::class, $envelope);
        $this->assertEquals('Resept går snart ut.', $envelope->subject);

        $from = $envelope->from;
        $this->assertInstanceOf(Address::class, $from);
        $this->assertEquals('admin@example.com', $from->address);
        $this->assertEquals('Admin User', $from->name);
    }

    #[Test]
    public function it_has_correct_content()
    {
        $mail = new SendReminderWhenPrescriptionExpire([], []);
        $content = $mail->content();

        $this->assertInstanceOf(Content::class, $content);
        $this->assertEquals('emails.prescription_expiry', $content->markdown);
    }

    #[Test]
    public function it_has_no_attachments()
    {
        $mail = new SendReminderWhenPrescriptionExpire([], []);
        $attachments = $mail->attachments();

        $this->assertIsArray($attachments);
        $this->assertEmpty($attachments);
    }

    #[Test]
    public function it_renders_with_prescription_data()
    {
        // $this->markTestSkipped('This test is skipped because the view is inconsistent - some parts use array syntax and others use object syntax.');

        $expiringPrescriptions = collect([
            [
                'id' => 1,
                'name' => 'Prescription 1',
                'validTo' => '2023-12-31',
            ],
        ]);

        $expiredPrescriptions = collect([
            [
                'id' => 2,
                'name' => 'Prescription 2',
                'validTo' => '2023-11-30',
            ],
        ]);

        $mail = new SendReminderWhenPrescriptionExpire($expiringPrescriptions, $expiredPrescriptions);

        // This test will fail if the view doesn't exist or can't be rendered with the given data
        $rendered = $mail->render();

        // We can't easily test the exact content without mocking the view,
        // but we can at least verify that it renders without errors
        $this->assertIsString($rendered);
    }
}
