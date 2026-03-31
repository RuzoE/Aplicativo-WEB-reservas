<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\RoomType;
use App\Rules\AllowedEmailDomain;
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

    public function store(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', new AllowedEmailDomain()],
            'check_in' => 'required|date',
            'check_out' => 'required|date',
            'room_id' => 'required|exists:rooms,id',
        ]);

        $email = $request->input('email');

        // 1. Determine user: Auth primary, else by Email
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
        $checkIn = \Carbon\Carbon::parse($request->input('check_in'));
        $checkOut = \Carbon\Carbon::parse($request->input('check_out'));
        $stayDays = $checkIn->diffInDays($checkOut);
        $totalAmount = $room->price * $stayDays;
        $downPayment = $totalAmount * 0.30;

        $order = new \App\Models\Order([
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'room_id' => null, // Ya no se pre-asigna una habitación específica
            'room_type_id' => $room->room_type_id, // Se guarda solo el tipo de habitación deseado
            'status' => 'pendiente',
            'down_payment_amount' => $downPayment,
            'is_paid' => false,
        ]);

        $user->orders()->save($order);

        registrarAuditoria(
            'CREATE',
            'reservas',
            $order->id,
            'Reserva creada para el usuario ID ' . $user->id . ' con check-in ' . $checkIn->format('Y-m-d') . ' y check-out ' . $checkOut->format('Y-m-d'),
            Auth::id() ?? $user->id
        );

        // Send email (always to the address provided in form for this specific reservation)
        try {
            \Illuminate\Support\Facades\Mail::to($email)->send(new \App\Mail\ReservationPendingMail($order));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Error sending reservation email: " . $e->getMessage());
        }

        return redirect()->route('orders.index')
            ->with('success', '¡Tu reserva ha sido recibida! Por favor revisa el correo (' . $email . ') para realizar el pago del anticipo y confirmar la reserva.');
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
            'status' => 'confirmada'
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
            ->with('success', '¡Pago de anticipo confirmado! Tu reserva ahora está CONFIRMADA.');
    }
}
