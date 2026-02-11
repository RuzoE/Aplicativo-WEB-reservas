{{-- resources/views/minibar/carrito/index.blade.php --}}
@extends('layouts.app')

@section('header')
    @include('layouts.header')
@endsection

@section('content')
<style>
    .cart-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 2rem;
    }

    .cart-header {
        margin-bottom: 2.5rem;
        padding-bottom: 1.5rem;
        border-bottom: 3px solid #f0ad4e;
    }

    .cart-header h1 {
        font-size: 2.5rem;
        font-weight: 700;
        color: #1a1a2e;
        margin: 0;
    }

    .empty-cart {
        text-align: center;
        padding: 4rem 2rem;
    }

    .empty-cart-icon {
        font-size: 5rem;
        color: #d0d0d0;
        margin-bottom: 1rem;
    }

    .empty-cart p {
        font-size: 1.2rem;
        color: #666;
        margin-bottom: 2rem;
    }

    .cart-content {
        display: grid;
        grid-template-columns: 1fr 380px;
        gap: 2rem;
    }

    @media (max-width: 768px) {
        .cart-content {
            grid-template-columns: 1fr;
        }
    }

    .cart-items {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    .cart-item {
        background: white;
        border: 1px solid #e8e8e8;
        border-radius: 12px;
        padding: 1.5rem;
        display: flex;
        gap: 1.5rem;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }

    .cart-item:hover {
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
        border-color: #f0ad4e;
    }

    .cart-item-image {
        flex-shrink: 0;
        width: 120px;
        height: 120px;
        background: #f5f5f5;
        border-radius: 8px;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .cart-item-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .cart-item-details {
        flex: 1;
    }

    .cart-item-name {
        font-size: 1.1rem;
        font-weight: 600;
        color: #1a1a2e;
        margin-bottom: 0.5rem;
    }

    .cart-item-type {
        font-size: 0.9rem;
        color: #888;
        margin-bottom: 1rem;
    }

    .cart-item-price {
        font-size: 1.3rem;
        font-weight: 700;
        color: #f0ad4e;
        margin-bottom: 1rem;
    }

    .cart-item-controls {
        display: flex;
        align-items: center;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .quantity-control {
        display: flex;
        align-items: center;
        border: 1px solid #e0e0e0;
        border-radius: 6px;
        background: #f9f9f9;
        padding: 0.25rem;
    }

    .quantity-control button {
        background: none;
        border: none;
        padding: 0.4rem 0.6rem;
        cursor: pointer;
        color: #666;
        font-size: 1rem;
        transition: all 0.2s;
    }

    .quantity-control button:hover {
        background: #f0ad4e;
        color: white;
    }

    .quantity-control span {
        padding: 0 1rem;
        font-weight: 600;
        min-width: 2rem;
        text-align: center;
        color: #333;
    }

    .cart-item-remove {
        margin-left: auto;
    }

    .remove-btn {
        background: #ff4757;
        color: white;
        border: none;
        padding: 0.6rem 1.2rem;
        border-radius: 6px;
        cursor: pointer;
        font-size: 0.9rem;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .remove-btn:hover {
        background: #ff3838;
        transform: translateY(-2px);
    }

    .cart-summary {
        position: sticky;
        top: 2rem;
        background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
        border-radius: 12px;
        padding: 2rem;
        color: white;
        height: fit-content;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
    }

    .summary-title {
        font-size: 1.2rem;
        font-weight: 700;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid rgba(255, 255, 255, 0.2);
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 0.8rem;
        font-size: 0.95rem;
    }

    .summary-row.total {
        font-size: 1.5rem;
        font-weight: 700;
        margin-top: 1.5rem;
        padding-top: 1.5rem;
        border-top: 2px solid rgba(255, 255, 255, 0.2);
        color: #ffc107;
    }

    .summary-row.total span:last-child {
        color: #ffc107;
    }

    .buttons-container {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.8rem;
        margin-top: 1.5rem;
    }

    .checkout-btn {
        grid-column: 1 / -1;
        background: linear-gradient(135deg, #f0ad4e 0%, #ff9800 100%);
        color: white;
        border: none;
        padding: 0.95rem;
        border-radius: 8px;
        font-size: 1rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.3s;
        box-shadow: 0 4px 12px rgba(240, 173, 78, 0.3);
        text-decoration: none;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.6rem;
    }

    .checkout-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 20px rgba(240, 173, 78, 0.4);
        text-decoration: none;
        color: white;
    }

    .continue-shopping-btn {
        background: white;
        color: #1a1a2e;
        border: 2px solid white;
        padding: 0.75rem;
        border-radius: 8px;
        font-size: 0.95rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        text-decoration: none;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }

    .continue-shopping-btn:hover {
        background: rgba(255, 255, 255, 0.9);
        text-decoration: none;
        color: #1a1a2e;
    }

    .alert {
        border-radius: 8px;
        padding: 1rem 1.5rem;
        margin-bottom: 1.5rem;
        border: none;
        font-weight: 500;
    }

    .alert-success {
        background: #d4edda;
        color: #155724;
    }

    .alert-danger {
        background: #f8d7da;
        color: #721c24;
    }
</style>

<div class="cart-container">
    <div class="cart-header">
        <h1>Mi Carrito</h1>
    </div>

    {{-- Mostrar alertas de éxito --}}
    @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    {{-- Mostrar alertas de error --}}
    @if(session('error'))
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif

    {{-- Verificar si hay items --}}
    @if($items->isEmpty())
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
                                    <button type="button" onclick="decreaseQty(this)" title="Disminuir cantidad">−</button>
                                    <span class="quantity-value">{{ $item['qty'] }}</span>
                                    <button type="button" onclick="increaseQty(this)" title="Aumentar cantidad">+</button>
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
