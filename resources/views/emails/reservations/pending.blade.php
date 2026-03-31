<x-mail::message>
# Información de tu Reserva

Hola,

Gracias por elegir **Hotel Oasis**. Tu reserva ha sido registrada y se encuentra en estado **pendiente**. Para confirmar tu estancia, es necesario realizar un pago de anticipo del **30%** del valor total.

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

Una vez confirmado el pago, recibirás un correo de confirmación y tu reserva pasará a estado **confirmada**.

Gracias,<br>
El equipo de **{{ config('app.name') }}**
</x-mail::message>

