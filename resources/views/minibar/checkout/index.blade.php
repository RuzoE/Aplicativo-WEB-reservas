@extends('layouts.app')

@section('header')
  @include('layouts.header')
@endsection

@section('content')
<style>
  .checkout-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem;
  }

  .page-header {
    margin-bottom: 3rem;
    padding-bottom: 2rem;
    border-bottom: 3px solid #f0ad4e;
  }

  .page-header h1 {
    font-size: 2.5rem;
    font-weight: 700;
    color: #1a1a2e;
    margin: 0;
  }

  .checkout-content {
    display: grid;
    grid-template-columns: 1fr 380px;
    gap: 2rem;
  }

  @media (max-width: 768px) {
    .checkout-content {
      grid-template-columns: 1fr;
    }
  }

  .order-summary-section {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
  }

  .summary-table {
    width: 100%;
    border-collapse: collapse;
  }

  .summary-table thead {
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
    color: white;
  }

  .summary-table th {
    padding: 1.2rem;
    text-align: left;
    font-weight: 600;
    border: none;
  }

  .summary-table th:nth-child(2),
  .summary-table th:nth-child(3),
  .summary-table th:nth-child(4) {
    text-align: right;
  }

  .summary-table tbody td {
    padding: 1.2rem;
    border-bottom: 1px solid #e8e8e8;
    color: #333;
  }

  .summary-table tbody tr:hover {
    background: #f9f9f9;
  }

  .summary-table tfoot td {
    padding: 1rem 1.2rem;
    background: #f5f5f5;
    border: none;
    font-weight: 600;
  }

  .summary-table tfoot td:first-child {
    text-align: right;
  }

  .summary-table tfoot .total-row {
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
    color: white;
    font-size: 1.2rem;
    font-weight: 700;
  }

  .summary-table tfoot .total-row td:last-child {
    color: #ffc107;
    font-size: 1.3rem;
  }

  .payment-section {
    background: white;
    border-radius: 12px;
    padding: 2rem;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
  }

  .payment-section h3 {
    font-size: 1.3rem;
    font-weight: 700;
    color: #1a1a2e;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 3px solid #f0ad4e;
    display: flex;
    align-items: center;
    gap: 0.7rem;
  }

  /* Inline error for payment method */
  .form-error{
    display:none;
    color:#b91c1c;
    background:#fde8e8;
    border:1px solid #f8b4b4;
    padding:.75rem 1rem;
    border-radius:8px;
    margin-bottom:1rem;
    font-weight:600
  }
  .payment-methods.error .payment-option label{border-color:#f39c12;box-shadow:0 0 0 3px rgba(255,153,0,.2)}

  .payment-methods {
    display: flex;
    flex-direction: column;
    gap: 1.2rem;
    margin-bottom: 2rem;
  }

  .payment-option {
    position: relative;
  }

  .payment-option input[type="radio"] {
    display: none;
  }

  .payment-option label {
    display: flex;
    align-items: center;
    gap: 1.2rem;
    padding: 1.4rem 1.8rem;
    border: 2.5px solid #e5e5e5;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
    background: #fafafa;
    font-weight: 600;
    color: #444;
    margin: 0;
    position: relative;
    overflow: hidden;
  }

  .payment-option label::after {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, rgba(240, 173, 78, 0.08) 0%, rgba(255, 152, 0, 0.08) 100%);
    transition: left 0.35s cubic-bezier(0.4, 0, 0.2, 1);
    pointer-events: none;
    z-index: 0;
  }

  .payment-option input[type="radio"]:checked + label::after {
    left: 0;
  }

  .payment-option input[type="radio"]:checked + label {
    border-color: #f0ad4e;
    background: white;
    color: #1a1a2e;
    box-shadow: 0 6px 20px rgba(240, 173, 78, 0.25);
    transform: translateY(-2px);
  }

  .payment-option label > * {
    position: relative;
    z-index: 1;
  }

  .payment-option label::before {
    content: '';
    width: 24px;
    height: 24px;
    min-width: 24px;
    border: 2.5px solid #ccc;
    border-radius: 50%;
    transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    z-index: 1;
  }

  .payment-option input[type="radio"]:checked + label::before {
    border-color: #f0ad4e;
    background: #f0ad4e;
    box-shadow: 0 0 0 4px rgba(240, 173, 78, 0.25);
    transform: scale(1.1);
  }

  .payment-icon {
    font-size: 1.8rem;
    position: relative;
    z-index: 1;
  }

  .payment-text {
    position: relative;
    z-index: 1;
    letter-spacing: 0.3px;
  }

  .notes-section {
    margin-bottom: 2rem;
  }

  .notes-section label {
    display: block;
    font-weight: 600;
    color: #1a1a2e;
    margin-bottom: 0.8rem;
    font-size: 1rem;
  }

  .notes-section textarea {
    width: 100%;
    padding: 1rem;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    font-family: inherit;
    resize: vertical;
    transition: all 0.3s;
    min-height: 100px;
  }

  .notes-section textarea:focus {
    outline: none;
    border-color: #f0ad4e;
    box-shadow: 0 0 0 3px rgba(240, 173, 78, 0.1);
  }

  .checkout-sidebar {
    position: sticky;
    top: 2rem;
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
    border-radius: 12px;
    padding: 2rem;
    color: white;
    height: fit-content;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
  }

  .sidebar-title {
    font-size: 1.2rem;
    font-weight: 700;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid rgba(255, 255, 255, 0.2);
  }

  .sidebar-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 1rem;
    font-size: 0.95rem;
  }

  .sidebar-row.total {
    font-size: 1.4rem;
    font-weight: 700;
    margin-top: 1.5rem;
    padding-top: 1.5rem;
    border-top: 2px solid rgba(255, 255, 255, 0.2);
    color: #ffc107;
  }

  .confirm-btn {
    width: 100%;
    background: linear-gradient(135deg, #27ae60 0%, #229954 100%);
    color: white;
    border: none;
    padding: 1.2rem;
    border-radius: 8px;
    font-size: 1.1rem;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s;
    margin-top: 2rem;
    box-shadow: 0 4px 12px rgba(39, 174, 96, 0.3);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.8rem;
  }

  .confirm-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(39, 174, 96, 0.4);
  }

  .back-link {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    color: #666;
    text-decoration: none;
    margin-bottom: 2rem;
    transition: all 0.3s;
  }

  .back-link:hover {
    color: #f0ad4e;
  }
</style>

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
          <button type="button" class="confirm-btn" onclick="submitCheckout()">
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

