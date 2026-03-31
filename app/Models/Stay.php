<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stay extends Model
{
    use HasFactory;

    protected $fillable = [
        'reservation_id',
        'user_id',
        'room_id',
        'guest_id',
        'status',
        'arrival_at',
        'departure_at',
        'actual_check_in_at',
        'actual_check_out_at',
        'adults',
        'children',
        'rate_plan',
        'daily_rate',
        'notes',
    ];

    protected $casts = [
        'arrival_at' => 'datetime',
        'departure_at' => 'datetime',
        'actual_check_in_at' => 'datetime',
        'actual_check_out_at' => 'datetime',
        'daily_rate' => 'decimal:2',
    ];

    public function guest()
    {
        return $this->belongsTo(Guest::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'reservation_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function folios()
    {
        return $this->hasMany(Folio::class);
    }

    /**
     * Extrae el numero de habitacion asignado desde las notas
     */
    public function getAssignedRoomNumberAttribute()
    {
        if ($this->notes && preg_match('/\[ROOM_NUM:(\d+)\]/', $this->notes, $matches)) {
            return (int) $matches[1];
        }

        // Fallback: Si no tiene en notas, intentar obtener el total_room del modelo Room real
        return $this->room ? $this->room->total_room : null;
    }

    public function getBillingBreakdown()
    {
        $order = $this->order;
        $folios = $this->folios;

        // 1. Reservation base (Total de la reserva por noches)
        // Usamos el total_amount del order si existe, o calculamos
        $reservationTotal = $order ? $order->total_amount : ($this->daily_rate * ($this->arrival_at->diffInDays($this->departure_at) ?: 1));
        
        // 2. Anticipo del 30% (Desde el Order)
        $downPayment = $order ? $order->down_payment_amount : 0;

        // 3. Cargos adicionales y pagos manuales (Desde el Folio)
        $additionalCharges = 0;
        $manualPayments = 0;
        $chargesDetail = [];
        $paymentsDetail = [];

        foreach ($folios as $folio) {
            $folioPayments = $folio->payments;
            foreach ($folio->charges as $charge) {
                // Identificar si el cargo ya fue pagado (ej: Minibar pagado al momento)
                $isPaid = false;
                if ($charge->source === 'Minibar' && $charge->reference_id) {
                    $isPaid = $folioPayments->contains(function($p) use ($charge) {
                        return $p->external_ref === 'Compra:' . $charge->reference_id;
                    });
                }
                
                $charge->status = $isPaid ? 'Pagado' : 'Pendiente';

                // Solo sumamos al subtotal de lo NO pagado lo que realmente está pendiente
                if (!$isPaid) {
                    // Nota: reservationTotal se maneja aparte al final
                }

                $additionalCharges += $charge->amount;
                
                if ($charge->source === 'Minibar' && $charge->reference_type === 'Compra') {
                    $compra = \App\Models\Compra::with('productos')->find($charge->reference_id);
                    if ($compra) {
                        $charge->details = $compra->productos;
                    }
                }
                
                $chargesDetail[] = $charge;
            }
            foreach ($folio->payments as $payment) {
                $manualPayments += $payment->amount;
                $paymentsDetail[] = $payment;
            }
        }

        // 4. Cálculos finales
        // Subtotal Informativo (Todo lo consumido)
        $subtotalTotal = $reservationTotal + $additionalCharges;
        $ivaTotal = $subtotalTotal * 0.19;
        $totalFinal = $subtotalTotal + $ivaTotal;
        
        // Subtotal Pendiente (Solo lo que no tiene pago registrado)
        // En este sistema, asumimos que la reserva (hospedaje) siempre está pendiente hasta el check-out 
        // a menos que tenga un anticipo (que se resta al final).
        $subtotalPendiente = $reservationTotal + collect($chargesDetail)->where('status', 'Pendiente')->sum('amount');
        $ivaPendiente = $subtotalPendiente * 0.19;

        $totalPaid = $manualPayments;
        
        // Check if an advance payment was already recorded in the folio (to avoid double counting)
        $hasAdvanceInFolio = collect($paymentsDetail)->contains(function($p) {
            return $p->external_ref && str_contains($p->external_ref, 'ANT-');
        });

        if (!$hasAdvanceInFolio) {
            $totalPaid += $downPayment;
        }

        $balanceRemaining = $totalFinal - $totalPaid;

        return (object) [
            'reservation_total' => $reservationTotal,
            'down_payment' => $downPayment,
            'additional_charges' => $additionalCharges,
            'manual_payments' => $manualPayments,
            'subtotal' => $subtotalTotal, // Se mantiene el total consumido por compatibilidad
            'subtotal_pendiente' => $subtotalPendiente,
            'iva' => $ivaTotal, // Se mantiene el total por compatibilidad
            'iva_pendiente' => $ivaPendiente,
            'total_final' => $totalFinal,
            'total_paid' => $totalPaid,
            'balance' => $balanceRemaining,
            'has_advance_in_folio' => $hasAdvanceInFolio,
            'charges_detail' => $chargesDetail,
            'payments_detail' => $paymentsDetail,
        ];
    }
}
