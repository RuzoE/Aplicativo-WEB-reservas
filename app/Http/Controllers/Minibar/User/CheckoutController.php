<?php

namespace App\Http\Controllers\Minibar\User;

use App\Http\Controllers\Controller;
use App\Models\Compra;
use App\Models\Stay;
use App\Services\Reception\FolioService;
use App\Events\Reception\ChargePosted;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
            $product = \App\Models\MinibarProduct::find($item['id']);

            if ($product) {
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
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('minibar.catalogo')
                             ->with('error', 'Tu carrito está vacío.');
        }

        DB::transaction(function () use ($cart, $request) {
            $compra = Compra::create([
                'user_id'     => auth()->id(), // ← corregido aquí
                'total'       => 0,
                'estado'      => 'completado',
                'metodo_pago' => $request->input('metodo_pago', 'efectivo'),
            ]);

            $total = 0;

            foreach ($cart as $item) {
                $prod = \App\Models\MinibarProduct::find($item['id']);
                $subtotal = $prod->precio * $item['qty'];

                $compra->productos()->attach($prod->id, [
                    'cantidad'        => $item['qty'],
                    'precio_unitario' => $prod->precio,
                ]);

                $total += $subtotal;
            }

            $compra->update(['total' => $total]);

            // Post charge to active stay folio if user has an active stay
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
                }
            }
        });

        // Limpiar carrito
        session()->forget('cart');

        return redirect()->route('minibar.catalogo')
                         ->with('success', 'Compra realizada con éxito.');
    }
}

