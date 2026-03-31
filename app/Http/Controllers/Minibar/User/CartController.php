<?php

namespace App\Http\Controllers\Minibar\User;

use App\Http\Controllers\Controller;
use App\Models\MinibarProduct;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);
        $cartUpdated = false;
        $stockAlerts = [];

        $items = collect($cart)->map(function ($item) use (&$cart, &$cartUpdated, &$stockAlerts) {
            $product = MinibarProduct::find($item['id']);

            if (!$product) {
                unset($cart[$item['id']]);
                $cartUpdated = true;
                return null;
            }

            $requestedQty = (int) $item['qty'];
            $availableStock = (int) $product->stock;

            if ($availableStock <= 0) {
                unset($cart[$item['id']]);
                $cartUpdated = true;
                $stockAlerts[] = 'Se removio "' . $product->nombre . '" del carrito porque ya no tiene stock.';
                return null;
            }

            if ($requestedQty > $availableStock) {
                $cart[$item['id']]['qty'] = $availableStock;
                $requestedQty = $availableStock;
                $cartUpdated = true;
                $stockAlerts[] = 'Se ajusto "' . $product->nombre . '" a ' . $availableStock . ' unidad(es) por stock disponible.';
            }

            if ($product) {
                return [
                    'product' => $product,
                    'qty' => $requestedQty,
                    'subtotal' => $product->precio * $requestedQty
                ];
            }

            return null;
        })->filter();

        if ($cartUpdated) {
            session()->put('cart', $cart);
        }

        if (!empty($stockAlerts)) {
            session()->flash('error', implode(' ', $stockAlerts));
        }

        $total = $items->sum('subtotal');

        return view('minibar.carrito.index', compact('items', 'total'));
    }

    public function add(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|exists:bebidas,id',
            'qty' => 'required|integer|min:1'
        ]);

        $cart = session()->get('cart', []);
        $id = $data['product_id'];
        $product = MinibarProduct::findOrFail($id);

        if ($product->stock <= 0) {
            return redirect()->back()->with('error', 'Producto agotado: ' . $product->nombre . '.');
        }

        $currentQty = isset($cart[$id]) ? (int) $cart[$id]['qty'] : 0;
        $newQty = $currentQty + (int) $data['qty'];

        if ($newQty > (int) $product->stock) {
            return redirect()->back()->with(
                'error',
                'Stock insuficiente para ' . $product->nombre . '. Disponible: ' . $product->stock . '.'
            );
        }

        $cart[$id] = ['id' => $id, 'qty' => $newQty];

        session()->put('cart', $cart);

        return redirect()->back()->with('success', 'Producto añadido al carrito.');
    }

    public function remove(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|exists:bebidas,id',
        ]);

        $cart = session()->get('cart', []);
        unset($cart[$data['product_id']]);
        session()->put('cart', $cart);

        return redirect()->back()->with('success', 'Producto eliminado del carrito.');
    }
}
