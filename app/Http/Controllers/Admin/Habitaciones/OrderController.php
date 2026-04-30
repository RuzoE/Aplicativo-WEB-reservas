<?php

namespace App\Http\Controllers\Admin\Habitaciones;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orders = Order::with(['user', 'room.roomtype'])->get();
        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
    //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
    //
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
    //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order)
    {
    //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $order)
    {
    //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        abort_unless(auth()->user()->hasRole('administrador'), 403, 'No autorizado para eliminar.');

        $orderId = $order->id;
        $order->delete();

        registrarAuditoria(
            'DELETE',
            'reservas',
            $orderId,
            'Reserva eliminada: ID ' . $orderId,
            auth()->id()
        );

        return redirect()->route('admin.habitaciones.reservas.index')
            ->with('message', 'La reserva ha sido eliminada.');
    }
}
