@extends('layouts.app')

@section('header')
  @include('layouts.header')
@endsection

@section('content')
<link rel="stylesheet" href="{{ asset('css/blade/minibar/bebida/show--style1.css') }}">

<div class="pd-container">
  <div class="pd-breadcrumb">
    <a href="{{ route('minibar.catalogo') }}">← Volver al catálogo</a>
  </div>

  <h1 class="pd-title">{{ $bebida->nombre }}</h1>

  <div class="pd-grid">
    <div class="pd-gallery">
      <div id="pd-viewport" class="pd-viewport">
        <img id="pd-image" src="{{ asset('storage/'.$bebida->imagen) }}" alt="{{ $bebida->nombre }}">
      </div>
    </div>

    <aside class="pd-info">
      <div class="pd-price">${{ number_format($bebida->precio, 2) }}</div>
      <div class="pd-meta">
        <span class="pd-badge">Tipo: {{ $bebida->type->nombre ?? '—' }}</span>
        @if($bebida->stock > 0)
          <span class="pd-badge pd-badge--stock">Stock: {{ $bebida->stock }}</span>
        @else
          <span class="pd-badge" style="background:#fde8e8;color:#b91c1c">Agotado</span>
        @endif
      </div>

      @if($bebida->stock > 0)
      <form action="{{ route('minibar.carrito.add') }}" method="POST">
        @csrf
        <input type="hidden" name="product_id" value="{{ $bebida->id }}">

        <div class="pd-qty">
          <label for="cantidad" class="form-label mb-0" style="min-width:80px;font-weight:800;color:#334155">Cantidad</label>
          <div class="pd-step">
            <button type="button" aria-label="Disminuir" class="js-pd-dec">−</button>
            <input id="cantidad" type="number" name="qty" min="1" max="{{ $bebida->stock }}" value="1">
            <button type="button" aria-label="Aumentar" class="js-pd-inc">+</button>
          </div>
        </div>

        <div class="pd-cta">
          <button type="submit" class="btn-gradient"><i class="fas fa-shopping-cart"></i> Añadir al carrito</button>
          <a class="btn-secondary-outline" href="{{ route('minibar.catalogo') }}">Volver al catálogo</a>
        </div>
      </form>
      @else
        <div class="pd-cta">
          <a class="btn-secondary-outline" href="{{ route('minibar.catalogo') }}">Volver al catálogo</a>
        </div>
      @endif
    </aside>
  </div>
</div>

<script src="{{ asset('js/minibar-producto.js') }}"></script>
@endsection

@section('footer')
  @include('layouts.footer')
@endsection


