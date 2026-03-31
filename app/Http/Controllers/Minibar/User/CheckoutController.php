<?php

namespace App\Http\Controllers\Minibar\User;

use App\Http\Controllers\Controller;
use App\Models\Compra;
use App\Models\Stay;
use App\Services\Reception\FolioService;
use App\Events\Reception\ChargePosted;
use App\Models\MinibarProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CheckoutController extends Controller
{
    // Formulario de checkout
    public function index()
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('minibar.catalogo')
                             ->with('error', 'Tu carrito está vacío.');
        }

        $items = [];
        $subtotal = 0;

        foreach ($cart as $item) {
            $product = MinibarProduct::find($item['id']);

            if ($product) {
                if ((int) $item['qty'] > (int) $product->stock) {
                    return redirect()->route('minibar.carrito.index')
                        ->with('error', 'Stock insuficiente para ' . $product->nombre . '. Disponible: ' . $product->stock . '.');
                }

                $lineaSubtotal = $product->precio * $item['qty'];

                $items[] = [
                    'product'  => $product,
                    'qty'      => $item['qty'],
                    'subtotal' => $lineaSubtotal
                ];

                $subtotal += $lineaSubtotal;
            }
        }

        $iva = $subtotal * 0.19;
        $total = $subtotal + $iva;

        return view('minibar.checkout.index', compact('items', 'subtotal', 'iva', 'total'));
    }

    // Procesar pago y crear compra
    public function pay(Request $request)
    {
        $request->validate([
            'metodo_pago' => 'nullable|string|max:50',
        ]);

        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('minibar.catalogo')
                             ->with('error', 'Tu carrito está vacío.');
        }

        try {
            DB::transaction(function () use ($cart, $request) {
                $compra = Compra::create([
                    'user_id'     => auth()->id(),
                    'total'       => 0,
                    'estado'      => 'completado',
                    'metodo_pago' => $request->input('metodo_pago', 'efectivo'),
                ]);

                $total = 0;

                foreach ($cart as $item) {
                    $qty = (int) $item['qty'];
                    $prod = MinibarProduct::lockForUpdate()->find($item['id']);

                    if (!$prod) {
                        throw ValidationException::withMessages([
                            'stock' => 'Uno de los productos ya no está disponible.',
                        ]);
                    }

                    if ($qty < 1) {
                        throw ValidationException::withMessages([
                            'stock' => 'Cantidad inválida para ' . $prod->nombre . '.',
                        ]);
                    }

                    if ($qty > (int) $prod->stock) {
                        throw ValidationException::withMessages([
                            'stock' => 'Stock insuficiente para ' . $prod->nombre . '. Disponible: ' . $prod->stock . '.',
                        ]);
                    }

                    $subtotal = $prod->precio * $qty;

                    $compra->productos()->attach($prod->id, [
                        'cantidad'        => $qty,
                        'precio_unitario' => $prod->precio,
                    ]);

                    $prod->decrement('stock', $qty);
                    $total += $subtotal;
                }

                $compra->update(['total' => $total]);

                registrarAuditoria(
                    'CREATE',
                    'minibar',
                    $compra->id,
                    'Venta de minibar registrada. Compra ID ' . $compra->id . ' por valor de ' . number_format($total, 2, '.', ''),
                    auth()->id()
                );

            // Post charge to active stay folio if user has an active stay
            // Prioritize the linked user_id in the stays table
            $activeStay = Stay::where('user_id', auth()->id())
                ->where('status', 'InHouse')
                ->with('folios')
                ->first();

            // Fallback for guests not yet linked but with matching email/document
            if (!$activeStay) {
                $activeStay = Stay::where('guest_id', function($query) {
                    $query->select('id')
                        ->from('guests')
                        ->where('email', auth()->user()->email)
                        ->orWhere('document_number', auth()->user()->document_number ?? 'N/A')
                        ->limit(1);
                })
                ->where('status', 'InHouse')
                ->with('folios')
                ->first();
            }

                if ($activeStay) {
                    $openFolio = $activeStay->folios()->where('status', 'Open')->first();

                    if ($openFolio) {
                        $folioService = app(FolioService::class);

                    $charge = $folioService->postCharge(
                        $openFolio,
                        [
                            'source' => 'Minibar',
                            'description' => 'Minibar Order #' . $compra->id,
                            'amount' => $total,
                            'tax' => $total * 0.19,
                            'reference_type' => 'Compra',
                            'reference_id' => $compra->id,
                        ],
                        auth()->user()
                    );

                        ChargePosted::dispatch($charge);

                    // If the minibar order was paid ("de una vez"), also register a payment in the folio
                    // This ensures the folio balance remains correct.
                        if ($compra->estado === 'completado') {
                            $payment = $folioService->receivePayment(
                                $openFolio,
                                [
                                    'method' => $compra->metodo_pago,
                                    'amount' => $total + ($total * 0.19), // Total with tax
                                    'currency' => 'USD',
                                    'description' => 'Pago Minibar - Orden #' . $compra->id,
                                    'external_ref' => 'Compra:' . $compra->id,
                                ],
                                auth()->user()
                            );

                            \App\Events\Reception\PaymentReceived::dispatch($payment);
                        }
                    }
                }
            });
        } catch (ValidationException $e) {
            return redirect()->route('minibar.carrito.index')
                ->with('error', collect($e->errors())->flatten()->first() ?: 'No se pudo procesar la compra por stock insuficiente.');
        }

        // Limpiar carrito
        session()->forget('cart');

        return redirect()->route('minibar.catalogo')
                         ->with('success', 'Compra realizada con éxito.');
    }
}

