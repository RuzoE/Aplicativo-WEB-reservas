<?php

namespace App\Services;

use App\Mail\ReservationPendingMail;
use App\Models\Order;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ReservationEmailService
{
    public function sendPendingPaymentInstructions(Order $order, string $email): bool
    {
        try {
            Mail::to($email)->send(new ReservationPendingMail($order));

            return true;
        } catch (\Throwable $e) {
            Log::error('No se pudo enviar el correo de instrucciones de anticipo para la reserva.', [
                'order_id' => $order->id,
                'email' => $email,
                'error' => $e->getMessage(),
                'class' => get_class($e),
            ]);

            return false;
        }
    }
}
