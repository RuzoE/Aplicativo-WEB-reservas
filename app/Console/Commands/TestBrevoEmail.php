<?php

namespace App\Console\Commands;

use App\Mail\ReservationPendingMail;
use App\Models\Order;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class TestBrevoEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:test-brevo {email?} {--order-id=1}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test email delivery using Brevo API transport';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        // Obtener email del parámetro o usar uno de prueba
        $email = $this->argument('email') ?? 'test@example.com';
        $orderId = $this->option('order-id');

        $this->info('🧪 Iniciando prueba de envío de correo vía Brevo...');
        $this->newLine();

        // Mostrar configuración
        $this->line('⚙️  Configuración actual:');
        $this->table(
            ['Parámetro', 'Valor'],
            [
                ['MAIL_MAILER', config('mail.default')],
                ['MAIL_FROM_ADDRESS', config('mail.from.address')],
                ['MAIL_FROM_NAME', config('mail.from.name')],
                ['BREVO_API_KEY', $this->maskApiKey(config('services.brevo.api_key'))],
            ]
        );
        $this->newLine();

        try {
            // Intentar obtener un Order existente
            $order = Order::find($orderId);

            if (!$order) {
                $this->warn("⚠️  No se encontró Order con ID {$orderId}. Creando uno de prueba...");
                $order = Order::factory()->create();
                $this->info("✅ Order de prueba creada con ID: {$order->id}");
            }

            // Enviar correo de prueba
            $this->info("\n📧 Enviando correo de reserva pendiente a: {$email}");
            Mail::to($email)->send(new ReservationPendingMail($order));

            $this->newLine();
            $this->info('✅ ¡Correo enviado exitosamente vía Brevo!');
            $this->line('Deberías recibir el email en 1-2 minutos.');

            Log::info('Email de prueba enviado vía Brevo', [
                'to' => $email,
                'order_id' => $order->id,
                'timestamp' => now(),
            ]);

            return self::SUCCESS;

        } catch (\Exception $e) {
            $this->newLine();
            $this->error('❌ Error al enviar el correo:');
            $this->error($e->getMessage());

            Log::error('Error al enviar email de prueba vía Brevo', [
                'to' => $email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return self::FAILURE;
        }
    }

    /**
     * Mask API key for display.
     */
    protected function maskApiKey(?string $apiKey): string
    {
        if (!$apiKey) {
            return '❌ NO CONFIGURADA';
        }

        if (strlen($apiKey) <= 10) {
            return str_repeat('*', strlen($apiKey));
        }

        return substr($apiKey, 0, 5) . str_repeat('*', strlen($apiKey) - 10) . substr($apiKey, -5);
    }
}
