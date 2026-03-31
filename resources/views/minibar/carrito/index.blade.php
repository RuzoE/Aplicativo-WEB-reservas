{{-- resources/views/minibar/carrito/index.blade.php --}}
@extends('layouts.app')

@section('header')
    @include('layouts.header')
@endsection

@section('content')
<link rel="stylesheet" href="{{ asset('css/blade/minibar/carrito/index--style1.css') }}">

<div class="cart-container">
    <div class="cart-header">
        <h1>Mi Carrito</h1>
    </div>

    @if($items->isEmpty())
        <div class="cart-content">
        <div class="empty-cart">
            <div class="empty-cart-icon">
                <i class="fas fa-shopping-cart"></i>
            </div>
            <p>Tu carrito está vacío</p>
            <a href="{{ route('minibar.catalogo') }}" class="btn btn-warning text-white fw-bold" style="padding: 0.8rem 2rem; font-size: 1.05rem;">
                <i class="fas fa-arrow-left"></i> Ir al catálogo
            </a>
        </div>
    @else
        <div class="cart-content">
            <div class="cart-items">
                @foreach($items as $item)
                    <div class="cart-item">
                        <div class="cart-item-image">
                            <img src="{{ asset('storage/'.$item['product']->imagen) }}"
                                 alt="{{ $item['product']->nombre }}">
                        </div>
                        <div class="cart-item-details">
                            <div class="cart-item-name">{{ $item['product']->nombre }}</div>
                            <div class="cart-item-type">{{ $item['product']->type->nombre ?? 'Bebida' }}</div>
                            <div class="cart-item-price">${{ number_format($item['product']->precio, 2) }}</div>
                            <div class="cart-item-controls">
                                <div class="quantity-control">
                                    <button type="button" class="js-qty-dec" title="Disminuir cantidad">−</button>
                                    <span class="quantity-value">{{ $item['qty'] }}</span>
                                    <button type="button" class="js-qty-inc" title="Aumentar cantidad">+</button>
                                </div>
                                <div style="color: #666; font-size: 0.95rem;">
                                    Subtotal: <strong style="color: #f0ad4e; font-size: 1.1rem;">${{ number_format($item['subtotal'], 2) }}</strong>
                                </div>
                            </div>
                        </div>
                        <div class="cart-item-remove">
                            <form action="{{ route('minibar.carrito.remove') }}" method="POST" style="margin: 0;">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $item['product']->id }}">
                                <button type="submit" class="remove-btn" title="Quitar producto del carrito">
                                    <i class="fas fa-trash-alt"></i> Quitar
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="cart-summary">
                <div class="summary-title">
                    <i class="fas fa-receipt"></i> Resumen
                </div>

                <div class="summary-row">
                    <span>Artículos:</span>
                    <span>{{ $items->sum('qty') }}</span>
                </div>

                <div class="summary-row total">
                    <span>Total:</span>
                    <span>${{ number_format($total, 2) }}</span>
                </div>

                <div class="buttons-container">
                    <a href="{{ route('minibar.checkout') }}" class="checkout-btn">
                        <i class="fas fa-lock"></i> Proceder al pago
                    </a>
                    <a href="{{ route('minibar.catalogo') }}" class="continue-shopping-btn">
                        <i class="fas fa-arrow-left"></i> Seguir comprando
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>

<script src="{{ asset('js/minibar-carrito.js') }}"></script>
@endsection

@section('footer')
    @include('layouts.footer')
@endsection


