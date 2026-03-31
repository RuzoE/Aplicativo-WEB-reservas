@extends('layouts.app')

@section('header')
  @include('layouts.header')
@endsection

@section('content')
<link rel="stylesheet" href="{{ asset('css/blade/minibar/checkout/index--style1.css') }}">

<div class="checkout-container">
  <a href="{{ route('minibar.carrito.index') }}" class="back-link">
    <i class="fas fa-arrow-left"></i> Volver al carrito
  </a>

  <div class="page-header">
    <h1>Confirmar Pedido</h1>
  </div>

  {{-- Mostrar resumen del carrito --}}
  @if(count($items) > 0)
    <div class="checkout-content">
      <div>
        <div class="order-summary-section">
          <table class="summary-table">
            <thead>
              <tr>
                <th>Bebida</th>
                <th>Precio unitario</th>
                <th>Cantidad</th>
                <th>Subtotal</th>
              </tr>
            </thead>
            <tbody>
              @foreach($items as $item)
                <tr>
                  <td>{{ $item['product']->nombre }}</td>
                  <td style="text-align: right;">${{ number_format($item['product']->precio, 2) }}</td>
                  <td style="text-align: center;">{{ $item['qty'] }}</td>
                  <td style="text-align: right;">${{ number_format($item['subtotal'], 2) }}</td>
                </tr>
              @endforeach
            </tbody>
            <tfoot>
              <tr>
                <td colspan="3">Subtotal</td>
                <td style="text-align: right;">${{ number_format($subtotal, 2) }}</td>
              </tr>
              <tr>
                <td colspan="3">IVA (19%)</td>
                <td style="text-align: right;">${{ number_format($iva, 2) }}</td>
              </tr>
              <tr class="total-row">
                <td colspan="3">Total</td>
                <td style="text-align: right;">${{ number_format($total, 2) }}</td>
              </tr>
            </tfoot>
          </table>
        </div>

        {{-- Formulario para pagar --}}
        <form action="{{ route('minibar.checkout.pay') }}" method="POST" class="payment-section" style="margin-top: 2rem;">
          @csrf

          {{-- Método de pago --}}
          <h3><i class="fas fa-credit-card"></i> Método de pago</h3>
          <div id="payment-error" class="form-error" role="alert">Por favor selecciona un método de pago.</div>

          <div class="payment-methods" id="payment-methods">
            <div class="payment-option">
              <input type="radio" class="btn-check" name="metodo_pago" id="efectivo" value="efectivo" required>
              <label for="efectivo">
                <span class="payment-icon">💵</span>
                <span class="payment-text">EFECTIVO</span>
              </label>
            </div>

            <div class="payment-option">
              <input type="radio" class="btn-check" name="metodo_pago" id="tarjeta" value="tarjeta" required>
              <label for="tarjeta">
                <span class="payment-icon">💳</span>
                <span class="payment-text">TARJETA DE CRÉDITO</span>
              </label>
            </div>

            <div class="payment-option">
              <input type="radio" class="btn-check" name="metodo_pago" id="pse" value="pse" required>
              <label for="pse">
                <span class="payment-icon">🏦</span>
                <span class="payment-text">TRANSFERENCIA PSE</span>
              </label>
            </div>
          </div>

          {{-- Notas para el pedido --}}
          <div class="notes-section">
            <label for="nota"><i class="fas fa-sticky-note"></i> Notas para el pedido</label>
            <textarea name="nota" id="nota" placeholder="Ej: Dejar en habitación 102..."></textarea>
          </div>
        </form>
      </div>

      <div class="checkout-sidebar">
        <div class="sidebar-title">
          <i class="fas fa-receipt"></i> Resumen
        </div>

        <div class="sidebar-row">
          <span>Artículos:</span>
          <span>{{ array_sum(array_column($items, 'qty')) }}</span>
        </div>

        <div class="sidebar-row">
          <span>Subtotal:</span>
          <span>${{ number_format($subtotal, 2) }}</span>
        </div>

        <div class="sidebar-row">
          <span>IVA (19%):</span>
          <span>${{ number_format($iva, 2) }}</span>
        </div>

        <div class="sidebar-row total">
          <span>Total:</span>
          <span>${{ number_format($total, 2) }}</span>
        </div>

        <form action="{{ route('minibar.checkout.pay') }}" method="POST" style="margin: 0;" id="checkout-form">
          @csrf
          <input type="hidden" name="metodo_pago" id="hidden_metodo_pago" value="">
          <input type="hidden" name="nota" id="hidden_nota" value="">
          <button type="button" class="confirm-btn js-checkout-submit">
            <i class="fas fa-check-circle"></i> Confirmar y Pagar
          </button>
        </form>
      </div>
    </div>
  @else
    <div style="text-align: center; padding: 4rem 2rem;">
      <i class="fas fa-shopping-cart" style="font-size: 4rem; color: #d0d0d0; margin-bottom: 1rem;"></i>
      <p style="font-size: 1.1rem; color: #666; margin-bottom: 2rem;">No tienes productos en tu carrito.</p>
      <a href="{{ route('minibar.catalogo') }}" class="btn btn-warning text-white fw-bold" style="padding: 0.8rem 2rem;">
        <i class="fas fa-arrow-left"></i> Volver al catálogo
      </a>
    </div>
  @endif
</div>

<script src="{{ asset('js/minibar-checkout.js') }}"></script>
@endsection

@section('footer')
  @include('layouts.footer')
@endsection



