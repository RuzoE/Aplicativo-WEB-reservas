<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Factura de Estancia #{{ $stay->id }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 14px; color: #333; margin: 0; padding: 20px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #ddd; padding-bottom: 10px; }
        .hotel-name { font-size: 24px; font-weight: bold; color: #1a2a40; margin: 0; }
        .hotel-info { font-size: 12px; color: #777; margin-top: 5px; }
        .invoice-title { font-size: 20px; font-weight: bold; margin-bottom: 5px; }
        
        .row { width: 100%; margin-bottom: 20px; clear: both; }
        .col-left { float: left; width: 50%; }
        .col-right { float: right; width: 50%; text-align: right; }
        .clear { clear: both; }

        .info-label { font-weight: bold; color: #555; }
        .info-value { color: #000; }

        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #f5f5f5; font-weight: bold; color: #333; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        
        .totals { width: 50%; float: right; margin-top: 20px; }
        .totals-row { display: flex; justify-content: space-between; padding: 5px 0; border-bottom: 1px solid #f5f5f5; }
        .totals-label { font-weight: bold; }
        .grand-total { font-size: 18px; font-weight: bold; color: #1a2a40; border-top: 2px solid #333; padding-top: 10px; }
        
        .footer { text-align: center; margin-top: 50px; font-size: 12px; color: #888; border-top: 1px solid #ddd; padding-top: 10px; }
    </style>
</head>
<body>

    <div class="header">
        <h1 class="hotel-name">HOTEL OASIS</h1>
        <div class="hotel-info">
            Avenida Principal #123, Ciudad<br>
            Teléfono: +57 123 456 7890 | Correo: contacto@hoteloasis.com<br>
            NIT: 900.123.456-7
        </div>
    </div>

    <div class="row">
        <div class="col-left">
            <h2 class="invoice-title">Cuentas de Cobro / Factura</h2>
            <div><span class="info-label">Fecha de Emisión:</span> <span class="info-value">{{ now()->format('d/m/Y H:i') }}</span></div>
            <div><span class="info-label">Estancia ID:</span> <span class="info-value">#{{ $stay->id }}</span></div>
            <div><span class="info-label">Habitación:</span> <span class="info-value">{{ $stay->room->number ?? 'N/A' }}</span></div>
        </div>
        <div class="col-right">
            <div><span class="info-label">Cliente:</span> <span class="info-value">{{ $stay->guest->name ?? 'N/A' }}</span></div>
            <div><span class="info-label">Documento:</span> <span class="info-value">{{ optional($stay->guest)->document_type ?? '' }} {{ optional($stay->guest)->document_number ?? 'N/A' }}</span></div>
            <div><span class="info-label">Fecha Check-in:</span> <span class="info-value">{{ \Carbon\Carbon::parse($stay->check_in)->format('d/m/Y') }}</span></div>
            <div><span class="info-label">Fecha Check-out:</span> <span class="info-value">{{ \Carbon\Carbon::parse($stay->check_out)->format('d/m/Y') }}</span></div>
        </div>
    </div>
    <div class="clear"></div>

    <h3>Detalle de Cargos</h3>
    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Concepto</th>
                <th class="text-right">Monto</th>
            </tr>
        </thead>
        <tbody>
            @php $totalCargos = 0; @endphp
            @foreach($stay->folios as $folio)
                @foreach($folio->charges as $charge)
                    <tr>
                        <td>{{ $charge->created_at->format('d/m/Y H:i') }}</td>
                        <td>{{ $charge->description }}</td>
                        <td class="text-right">${{ number_format($charge->amount, 2) }}</td>
                    </tr>
                    @php $totalCargos += $charge->amount; @endphp
                @endforeach
            @endforeach
            
            @if($totalCargos == 0)
                <tr>
                    <td colspan="3" class="text-center">No hay cargos registrados.</td>
                </tr>
            @endif
        </tbody>
    </table>

    <h3>Detalle de Pagos</h3>
    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Método de Pago</th>
                <th>Concepto</th>
                <th class="text-right">Monto Pagado</th>
            </tr>
        </thead>
        <tbody>
            @php $totalPagos = 0; @endphp
            @foreach($stay->folios as $folio)
                @foreach($folio->payments as $payment)
                    <tr>
                        <td>{{ $payment->created_at->format('d/m/Y H:i') }}</td>
                        <td>{{ $payment->payment_method ?? 'N/A' }}</td>
                        <td>{{ $payment->description ?? 'Pago/Abono' }}</td>
                        <td class="text-right">${{ number_format($payment->amount, 2) }}</td>
                    </tr>
                    @php $totalPagos += $payment->amount; @endphp
                @endforeach
            @endforeach
            
            @if($totalPagos == 0)
                <tr>
                    <td colspan="4" class="text-center">No hay pagos registrados.</td>
                </tr>
            @endif
        </tbody>
    </table>

    <div class="totals">
        <table style="border: none; margin-top: 0;">
            <tr style="border: none;">
                <td style="border: none; text-align: right;" class="totals-label">Total Cargos:</td>
                <td style="border: none;" class="text-right">${{ number_format($totalCargos, 2) }}</td>
            </tr>
            <tr style="border: none;">
                <td style="border: none; text-align: right;" class="totals-label">Total Pagos:</td>
                <td style="border: none;" class="text-right">${{ number_format($totalPagos, 2) }}</td>
            </tr>
            <tr style="border: none;">
                <td style="border: none; text-align: right;" class="totals-label grand-total">Saldo Final:</td>
                <td style="border: none;" class="text-right grand-total">${{ number_format(max(0, $totalCargos - $totalPagos), 2) }}</td>
            </tr>
        </table>
    </div>
    <div class="clear"></div>

    <div class="footer">
        Gracias por su visita. Esperamos verle pronto.<br>
        Este documento es un comprobante de pago generado por el sistema Hotel Oasis.
    </div>

</body>
</html>
