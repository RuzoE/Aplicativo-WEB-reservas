@php $billing = $stay->getBillingBreakdown(); @endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Comprobante de Pago #{{ preg_replace('/\D+/', '', (string) ($invoice->invoice_number ?? $stay->id)) }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 13px; color: #333; margin: 0; padding: 20px; line-height: 1.4; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 3px solid #f0ad4e; padding-bottom: 15px; }
        .hotel-name { font-size: 26px; font-weight: bold; color: #1a1a2e; margin: 0; text-transform: uppercase; letter-spacing: 1px; }
        .hotel-info { font-size: 11px; color: #666; margin-top: 5px; }

        .invoice-header { margin-bottom: 25px; background: #f9f9f9; padding: 15px 20px; border-radius: 8px; border: 1px solid #e0e0e0; }
        .invoice-title { font-size: 18px; font-weight: bold; color: #1a1a2e; margin: 0 0 12px 0; border-bottom: 1px solid #ddd; padding-bottom: 8px; }

        .row { width: 100%; clear: both; }
        .col-6 { float: left; width: 48%; }
        .clear { clear: both; }

        .info-group { margin-bottom: 6px; }
        .info-label { font-weight: bold; color: #555; display: inline-block; min-width: 110px; }
        .info-value { color: #000; }

        table { width: 100%; border-collapse: collapse; margin-top: 15px; border-radius: 6px; overflow: hidden; }
        th, td { border: 1px solid #e0e0e0; padding: 10px 12px; text-align: left; }
        th { background-color: #1a1a2e; font-weight: bold; color: #ffffff; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; }
        td { background-color: #fff; }
        tr:nth-child(even) td { background-color: #f9f9f9; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }

        .section-title { font-size: 14px; font-weight: bold; color: #1a1a2e; margin: 25px 0 0 0; border-left: 4px solid #f0ad4e; padding-left: 10px; }

        .totals-container { width: 45%; float: right; margin-top: 25px; }
        .totals-table { border: none; margin-top: 0; width: 100%; }
        .totals-table td { border: none; padding: 5px 10px; background: transparent; }
        .totals-label { font-weight: bold; color: #555; white-space: nowrap; }
        .totals-value { font-weight: normal; color: #000; }
        .grand-total { font-size: 16px; font-weight: bold; color: #1a1a2e; background: #fff8e1 !important; border-top: 2px solid #f0ad4e !important; border-bottom: 2px solid #f0ad4e !important; }
        .grand-total td { padding: 12px 10px !important; background: transparent !important; }

        .footer { text-align: center; margin-top: 60px; font-size: 11px; color: #888; border-top: 1px solid #eee; padding-top: 15px; clear: both; }
        .badge { display: inline-block; padding: 3px 10px; border-radius: 4px; font-size: 10px; font-weight: bold; text-transform: uppercase; }
        .badge-pending { background: #fff3e0; color: #e65100; }
        .badge-success { background: #e8f5e9; color: #2e7d32; }
    </style>
</head>
<body>
    @php
        $comprobanteNumber = preg_replace('/\D+/', '', (string) ($invoice->invoice_number ?? $stay->id));
    @endphp

    <div class="header">
        <div class="hotel-info">
            <h1 class="hotel-name">HOTEL OASIS DE LA COLINA</h1>
            Colina Dg 9 #22 - 10, Girardot-Cundinamarca, Colombia<br>
            Teléfono: +57 324 285 5624 | Correo: Hoteloasisreservas1@gmail.com<br>
            NIT: 900.000.000-1 | Régimen Común
        </div>
    </div>

    <div class="invoice-header">
        <h2 class="invoice-title">Comprobante</h2>
        <div class="row">
            <div class="col-6">
                <div class="info-group">
                    <span class="info-label">Comprobante de Pago N°:</span>
                    <span class="info-value">{{ $comprobanteNumber }}</span>
                </div>
                <div class="info-group">
                    <span class="info-label">Fecha Emisión:</span>
                    <span class="info-value">{{ now()->format('d/m/Y h:i A') }}</span>
                </div>
                <div class="info-group">
                    <span class="info-label">Habitación:</span>
                    <span class="info-value">#{{ $stay->assigned_room_number ?? $stay->room->total_room }}</span>
                </div>
            </div>
            <div class="col-6" style="text-align: right;">
                <div class="info-group">
                    <span class="info-label">Cliente:</span>
                    <span class="info-value">{{ optional($stay->guest)->first_name }} {{ optional($stay->guest)->last_name }}</span>
                </div>
                <div class="info-group">
                    <span class="info-label">Documento:</span>
                    <span class="info-value">{{ optional($stay->guest)->document_type }} {{ optional($stay->guest)->document_number }}</span>
                </div>
                <div class="info-group">
                    <span class="info-label">Estancia:</span>
                    <span class="info-value">{{ $stay->arrival_at->format('d/m/Y') }} al {{ $stay->departure_at->format('d/m/Y') }}</span>
                </div>
            </div>
        </div>
        <div class="clear"></div>
    </div>

    <div class="section-title">Detalle de Servicios y Cargos</div>
    <table>
        <thead>
            <tr>
                <th>Descripción / Concepto</th>
                <th class="text-center">Cant/Noches</th>
                <th class="text-right">V. Unitario</th>
                <th class="text-right">Subtotal</th>
                <th class="text-center">Estado</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Hospedaje - Tarifa Diaria</td>
                <td class="text-center">{{ $stay->arrival_at->diffInDays($stay->departure_at) ?: 1 }}</td>
                <td class="text-right">${{ number_format($stay->daily_rate, 2) }}</td>
                <td class="text-right">${{ number_format($billing->reservation_total, 2) }}</td>
                <td class="text-center"><span class="badge badge-pending">Pendiente</span></td>
            </tr>
            @foreach($billing->charges_detail as $charge)
            @php $isPaid = $charge->status === 'Pagado'; @endphp
            @if(isset($charge->details) && count($charge->details) > 0)
                @foreach($charge->details as $prod)
                <tr>
                    <td>{{ $prod->nombre }} (Minibar)</td>
                    <td class="text-center">{{ $prod->pivot->cantidad }}</td>
                    <td class="text-right">${{ number_format($prod->pivot->precio_unitario, 2) }}</td>
                    <td class="text-right">${{ number_format($prod->pivot->cantidad * $prod->pivot->precio_unitario, 2) }}</td>
                    <td class="text-center">
                        <span class="badge {{ $isPaid ? 'badge-success' : 'badge-pending' }}">
                            {{ $charge->status }}
                        </span>
                    </td>
                </tr>
                @endforeach
            @else
            <tr>
                <td>{{ $charge->description }} ({{ $charge->source }})</td>
                <td class="text-center">1</td>
                <td class="text-right">${{ number_format($charge->amount, 2) }}</td>
                <td class="text-right">${{ number_format($charge->amount, 2) }}</td>
                <td class="text-center">
                    <span class="badge {{ $isPaid ? 'badge-success' : 'badge-pending' }}">
                        {{ $charge->status }}
                    </span>
                </td>
            </tr>
            @endif
            @endforeach
        </tbody>
    </table>

    <div class="section-title">Detalle de Pagos y Abonos</div>
    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Método de Pago</th>
                <th>Descripción</th>
                <th class="text-right">Monto</th>
            </tr>
        </thead>
        <tbody>
            @if($billing->down_payment > 0 && !$billing->has_advance_in_folio)
            <tr>
                <td>{{ optional($stay->order)->created_at ? $stay->order->created_at->format('d/m/Y') : 'N/A' }}</td>
                <td>Anticipo (Confirmación)</td>
                <td>Pago Inicial 30% Reserva</td>
                <td class="text-right">${{ number_format($billing->down_payment, 2) }}</td>
            </tr>
            @endif
            @foreach($billing->payments_detail as $payment)
            <tr>
                <td>{{ $payment->created_at->format('d/m/Y') }}</td>
                <td>{{ $payment->method ?? 'Pago Manual' }}</td>
                <td>
                    @if(str_starts_with($payment->external_ref, 'ANT'))
                        <strong>{{ $payment->description ?: 'Abono Inicial 30% Reserva' }}</strong>
                    @else
                        {{ $payment->description ?: 'Abono a estancia' }}
                    @endif

                    @if($payment->external_ref)
                        <br><small style="color: #666;">Ref: {{ $payment->external_ref }}</small>
                    @endif
                </td>
                <td class="text-right">${{ number_format($payment->amount, 2) }}</td>
            </tr>
            @endforeach
            @if($billing->total_paid == 0)
            <tr>
                <td colspan="4" class="text-center">No se registran pagos previos.</td>
            </tr>
            @endif
        </tbody>
    </table>

    <div class="totals-container">
        <table class="totals-table">
            <tr>
                <td class="totals-label text-right">Subtotal Pendiente:</td>
                <td class="text-right totals-value">${{ number_format($billing->subtotal_pendiente, 2) }}</td>
            </tr>
            <tr>
                <td class="totals-label text-right">IVA (19%):</td>
                <td class="text-right totals-value">${{ number_format($billing->iva_pendiente, 2) }}</td>
            </tr>
            <tr class="grand-total">
                <td class="totals-label text-right">TOTAL A PAGAR:</td>
                <td class="text-right">${{ number_format($billing->balance, 2) }}</td>
            </tr>
            <tr>
                <td colspan="2" style="font-size: 10px; color: #888; padding-top: 15px;">
                    <strong>Resumen Transacciones Totales:</strong><br>
                    Consumos Totales (+IVA): ${{ number_format($billing->total_final, 2) }}<br>
                    Pagos y Abonos Aplicados: -${{ number_format($billing->total_paid, 2) }}
                </td>
            </tr>
        </table>
    </div>
    <div class="clear"></div>

    <div class="footer">
        <strong>¡Gracias por elegir Hotel Oasis!</strong><br>
        Este documento es un comprobante oficial de su estancia. Para cualquier duda, contáctenos.<br>
        <span style="font-size: 9px; color: #aaa; margin-top: 10px; display: block;">Generado por Sistema de Gestión Hotelera SAM - {{ date('Y') }}</span>
    </div>

</body>
</html>


