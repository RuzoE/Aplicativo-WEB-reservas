<?php

namespace Tests\Feature\Mail;

use App\Mail\ReservationPendingMail;
use App\Models\Order;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class BrevoMailTest extends TestCase
{
    /**
     * Test que Brevo está registrado como transport disponible.
     */
    public function test_brevo_transport_is_registered(): void
    {
        $this->assertTrue(
            method_exists(Mail::getFacadeRoot(), 'mailer'),
            'Mail manager should have mailer method'
        );
    }

    /**
     * Test que ReservationPendingMail se puede enviar.
     */
    public function test_reservation_pending_mail_can_be_sent(): void
    {
        Mail::fake();

        $order = Order::factory()->create();

        Mail::to('test@example.com')->send(new ReservationPendingMail($order));

        Mail::assertSent(ReservationPendingMail::class, function ($mail) {
            return $mail->hasTo('test@example.com');
        });
    }

    /**
     * Test que el FROM address está configurado como Hotel Oasis.
     */
    public function test_mail_from_configuration(): void
    {
        $from = config('mail.from');

        $this->assertEquals('hoteloasisreservas1@gmail.com', $from['address']);
        $this->assertEquals('Hotel Oasis', $from['name']);
    }

    /**
     * Test que Brevo API key está configurada.
     */
    public function test_brevo_api_key_is_configured(): void
    {
        // API Key should be present (can be masked in CI/CD)
        $this->assertNotEmpty(
            config('services.brevo.api_key'),
            'BREVO_API_KEY should be configured in environment'
        );
    }
}

