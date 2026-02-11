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

        $items = collect($cart)->map(function ($item) {
            $product = MinibarProduct::find($item['id']);
            if ($product) {
                return [
                    'product' => $product,
                    'qty' => $item['qty'],
                    'subtotal' => $product->precio * $item['qty']
                ];
            }
            return null;
        })->filter();

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

        if (isset($cart[$id])) {
            $cart[$id]['qty'] += $data['qty'];
        } else {
            $cart[$id] = ['id' => $id, 'qty' => $data['qty']];
        }

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
