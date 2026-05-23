<x-mail::message>
# Información de tu Reserva

Hola,

Gracias por elegir **Hotel Oasis**. Tu reserva ha sido registrada y quedó en estado **pendiente_pago**. Para continuar con la reserva, es necesario realizar el pago del anticipo del **30%** del valor total.

### Resumen de la Reserva
---
**Habitación:** {{ $order->roomType->name ?? 'Estándar' }}
**Check-in:** {{ $order->check_in->format('d/m/Y') }}
**Check-out:** {{ $order->check_out->format('d/m/Y') }}

---
**Precio Total:** @cop($order->total_amount)
**Valor del Anticipo (30%):** **@cop($downPayment)**

Para realizar el pago con tarjeta o transferencia de forma segura, haz clic en el siguiente enlace:

<x-mail::button :url="$paymentUrl" color="success">
Realizar Pago de Anticipo
</x-mail::button>

Si tienes alguna duda, puedes contactarnos directamente al teléfono de atención:
**+57 324 285 5624**

Una vez confirmado el pago, tu reserva pasará a estado **reserva_previa** y quedará lista para la siguiente etapa del proceso.

Gracias,<br>
El equipo de **{{ config('app.name') }}**
</x-mail::message>

