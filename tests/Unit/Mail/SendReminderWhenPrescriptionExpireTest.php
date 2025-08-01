<?php

namespace Tests\Unit\Mail;

use App\Mail\SendReminderWhenPrescriptionExpire;
use App\Models\Resepter;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SendReminderWhenPrescriptionExpireTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Opprett admin-rolle og bruker
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $adminUser = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
        ]);
        $adminUser->assignRole($adminRole);
    }

    #[Test]
    public function it_has_correct_properties(): void
    {
        $expiringPrescriptions = collect([new Resepter(['prescription1']), new Resepter(['prescription2'])]);
        $expiredPrescriptions = collect([new Resepter(['prescription3']), new Resepter(['prescription4'])]);

        $mail = new SendReminderWhenPrescriptionExpire($expiringPrescriptions, $expiredPrescriptions);

        $this->assertEquals($expiringPrescriptions, $mail->expiringPrescriptions);
        $this->assertEquals($expiredPrescriptions, $mail->expiredPrescriptions);
    }

    #[Test]
    public function it_has_correct_envelope(): void
    {
        // Test med tomme samlinger (default case)
        $mail = new SendReminderWhenPrescriptionExpire(collect(), collect());
        $envelope = $mail->envelope();
        $this->assertEquals('Resept påminnelse', $envelope->subject);

        // Test med bare utløpende resepter
        $mail = new SendReminderWhenPrescriptionExpire(collect([new Resepter]), collect());
        $envelope = $mail->envelope();
        $this->assertEquals('Resepter går snart ut', $envelope->subject);

        // Test med bare utløpte resepter
        $mail = new SendReminderWhenPrescriptionExpire(collect(), collect([new Resepter]));
        $envelope = $mail->envelope();
        $this->assertEquals('Resepter har utløpt', $envelope->subject);

        // Test med både utløpte og utløpende resepter
        $mail = new SendReminderWhenPrescriptionExpire(collect([new Resepter]), collect([new Resepter]));
        $envelope = $mail->envelope();
        $this->assertEquals('Resepter har utløpt og går snart ut', $envelope->subject);

        // Verifiser avsenderinformasjon
        $this->assertEquals('admin@example.com', $envelope->from->address);
        $this->assertEquals('Admin User', $envelope->from->name);
    }

    #[Test]
    public function it_has_correct_content(): void
    {
        $mail = new SendReminderWhenPrescriptionExpire(collect(), collect());
        $content = $mail->content();

        $this->assertEquals('emails.prescription_expiry', $content->markdown);
    }

    #[Test]
    public function it_has_no_attachments(): void
    {
        $mail = new SendReminderWhenPrescriptionExpire(collect(), collect());
        $attachments = $mail->attachments();

        $this->assertIsArray($attachments);
        $this->assertEmpty($attachments);
    }

    #[Test]
    public function it_renders_with_prescription_data(): void
    {
        $expiringPrescriptions = collect([
            new Resepter(['name' => 'Prescription 1', 'validTo' => '2023-12-31']),
        ]);

        $expiredPrescriptions = collect([
            new Resepter(['name' => 'Prescription 2', 'validTo' => '2023-11-30']),
        ]);

        $mail = new SendReminderWhenPrescriptionExpire($expiringPrescriptions, $expiredPrescriptions);

        try {
            $rendered = $mail->render();
            $this->assertNotEmpty($rendered);
        } catch (\Throwable $e) {
            $this->fail('Render feilet: '.$e->getMessage());
        }
    }
}
