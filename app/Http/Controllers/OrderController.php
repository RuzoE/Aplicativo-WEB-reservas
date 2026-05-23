<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Rules\AllowedEmailDomain;
use App\Services\ReservationAvailabilityService;
use App\Services\ReservationEmailService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth')->except(['store', 'paymentPage', 'confirmPayment']);
    }

    public function index()
    {
        $user = Auth::user();
        if (!$user) return redirect()->route('login');

        $orders = $user->orders()->with(['room.roomtype', 'roomType'])->orderBy('check_in', 'DESC')->get();
        return view('pages.list-orders', ['orders' => $orders]);
    }

    public function store(Request $request, ReservationEmailService $reservationEmailService)
    {
        $request->validate([
            'email' => ['required', 'email', new AllowedEmailDomain()],
            'check_in' => 'required|date',
            'check_out' => 'required|date',
            'room_id' => 'required|exists:rooms,id',
        ]);

        $email = $request->input('email');

        if (Auth::check()) {
            $user = Auth::user();
        } else {
            $user = \App\Models\User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => explode('@', $email)[0],
                    'password' => \Illuminate\Support\Facades\Hash::make(\Illuminate\Support\Str::random(16)),
                ]
            );
            Auth::login($user);
        }

        $room = \App\Models\Room::findOrFail($request->input('room_id'));
        $checkIn = Carbon::parse($request->input('check_in'));
        $checkOut = Carbon::parse($request->input('check_out'));
        $stayDays = $checkIn->diffInDays($checkOut);
        $totalAmount = $room->price * $stayDays;
        $downPayment = $totalAmount * 0.30;

        $order = Order::create([
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'room_id' => null,
            'room_type_id' => $room->room_type_id,
            'user_id' => $user->id,
            'status' => Order::STATUS_PENDIENTE_PAGO,
            'down_payment_amount' => $downPayment,
            'is_paid' => false,
        ]);

        registrarAuditoria(
            'CREATE',
            'reservas',
            $order->id,
            'Reserva creada para el usuario ID ' . $user->id . ' con check-in ' . $checkIn->format('Y-m-d') . ' y check-out ' . $checkOut->format('Y-m-d') . ' y estado pendiente_pago',
            Auth::id() ?? $user->id
        );

        $emailSent = $reservationEmailService->sendPendingPaymentInstructions($order, $email);

        $message = $emailSent
            ? '¡Tu reserva ha sido recibida y quedó en estado pendiente_pago! Revisa el correo (' . $email . ') para realizar el pago del anticipo y confirmar tu reserva.'
            : '¡Tu reserva ha sido recibida y quedó en estado pendiente_pago! No pudimos enviar el correo de confirmación en este momento, pero tu reserva está registrada. Contacta al hotel para más detalles.';

        return redirect()->route('orders.index')
            ->with($emailSent ? 'success' : 'warning', $message);
    }

    public function paymentPage($token)
    {
        $order = \App\Models\Order::where('payment_token', $token)->firstOrFail();

        if ($order->is_paid) {
            return redirect()->route('orders.index')->with('info', 'Esta reserva ya ha sido pagada.');
        }

        // Security check: if logged in, must be the owner
        if (Auth::check() && Auth::id() !== $order->user_id) {
            return redirect()->route('orders.index')->with('error', 'No tienes permiso para pagar esta reserva.');
        }

        return view('pages.payment', compact('order'));
    }

    public function confirmPayment(Request $request, $token)
    {
        $order = \App\Models\Order::where('payment_token', $token)->firstOrFail();

        if ($order->is_paid) {
            return redirect()->route('orders.index')->with('info', 'Esta reserva ya ha sido pagada.');
        }

        // Security check: if logged in, must be the owner
        if (Auth::check() && Auth::id() !== $order->user_id) {
            return redirect()->route('orders.index')->with('error', 'No tienes permiso para confirmar esta reserva.');
        }

        // Simulate payment success
        $estadoAnterior = $order->status;
        $order->update([
            'is_paid' => true,
            'status' => Order::STATUS_RESERVA_PREVIA,
        ]);

        registrarAuditoria(
            'UPDATE',
            'reservas',
            $order->id,
            'Reserva confirmada por pago de anticipo. Estado: ' . $estadoAnterior . ' -> ' . $order->status,
            Auth::id() ?? $order->user_id
        );

        // Auto-login correct user if session doesn't match
        if (!Auth::check() || Auth::id() !== $order->user_id) {
            Auth::loginUsingId($order->user_id);
        }

        return redirect()->route('orders.index')
            ->with('success', '¡Pago de anticipo confirmado! Tu reserva ahora está en estado reserva_previa.');
    }

    /**
     * Show the form for editing the order dates.
     */
    public function edit(Order $user_order)
    {
        $order = $user_order; // Alias for the rest of the code
        // Security check: must be the owner
        if (Auth::id() !== $order->user_id) {
            return redirect()->route('orders.index')->with('error', 'No tienes permiso para modificar esta reserva.');
        }

        // Only allow editing if not finalized/cancelled
        if (in_array($order->status, ['finalizada', 'cancelada'])) {
            return redirect()->route('orders.index')->with('error', 'Esta reserva no puede ser modificada en su estado actual.');
        }

        $duration = $order->check_in->diffInDays($order->check_out);

        return view('pages.edit-order', compact('order', 'duration'));
    }

    /**
     * Update the order dates.
     */
    public function update(Request $request, Order $user_order, ReservationAvailabilityService $availabilityService)
    {
        $order = $user_order; // Alias
        // Security check: must be the owner
        if (Auth::id() !== $order->user_id) {
            return redirect()->route('orders.index')->with('error', 'No tienes permiso para modificar esta reserva.');
        }

        $request->validate([
            'check_in' => 'required|date|after_or_equal:today',
        ]);

        $newCheckIn = Carbon::parse($request->input('check_in'))->startOfDay();
        $originalDuration = $order->check_in->diffInDays($order->check_out);
        $newCheckOut = $newCheckIn->copy()->addDays($originalDuration);

        // Availability check
        if (!$availabilityService->isAvailable($order->room_type_id, $newCheckIn, $newCheckOut, $order->id)) {
            return back()->with('error', 'No hay disponibilidad para las nuevas fechas seleccionadas.')
                         ->withInput();
        }

        $oldCheckIn = $order->check_in->format('Y-m-d');
        $oldCheckOut = $order->check_out->format('Y-m-d');

        $order->update([
            'check_in' => $newCheckIn,
            'check_out' => $newCheckOut,
        ]);

        registrarAuditoria(
            'UPDATE',
            'reservas',
            $order->id,
            "Reserva modificada por el usuario. Check-in: $oldCheckIn -> {$newCheckIn->format('Y-m-d')}, Check-out: $oldCheckOut -> {$newCheckOut->format('Y-m-d')}",
            Auth::id()
        );

        return redirect()->route('orders.index')
            ->with('success', 'La fecha de tu reserva ha sido actualizada correctamente.');
    }
}
