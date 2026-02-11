@extends('layouts.app')

@section('header')
  @include('layouts.header')
@endsection

@section('content')
<style>
  .pd-container{max-width:1200px;margin:0 auto;padding:2rem 1rem}
  .pd-breadcrumb{display:flex;align-items:center;gap:.5rem;margin-bottom:1rem}
  .pd-breadcrumb a{color:#f0ad4e;text-decoration:none;font-weight:700}
  .pd-title{font-size:2.2rem;font-weight:900;color:#1a1a2e;margin:0 0 .75rem}
  .pd-grid{display:grid;grid-template-columns:1fr 460px;gap:2rem;align-items:start}
  .pd-viewport{background:#fff;border-radius:14px;box-shadow:0 6px 16px rgba(0,0,0,.08);height:520px;display:flex;align-items:center;justify-content:center;overflow:hidden}
  .pd-viewport img{width:100%;height:100%;object-fit:contain;transition:transform .25s,transform-origin .25s}
  .pd-info{position:sticky;top:1.25rem;background:#fff;border-radius:14px;box-shadow:0 6px 16px rgba(0,0,0,.08);padding:1.25rem}
  .pd-price{font-size:2rem;font-weight:900;color:#f0ad4e;margin:.25rem 0 1rem}
  .pd-meta{display:flex;flex-wrap:wrap;gap:.6rem;margin-bottom:1rem}
  .pd-badge{background:#eef2f7;color:#334155;border-radius:999px;padding:.35rem .7rem;font-size:.85rem;font-weight:800}
  .pd-badge--stock{background:#e8f7ef;color:#1e7e34}
  .pd-qty{display:flex;align-items:center;gap:.6rem;margin:1rem 0}
  .pd-step{display:flex;align-items:center;border:2px solid #e5e7eb;border-radius:12px;overflow:hidden}
  .pd-step button{all:unset;background:#f7f7f8;padding:.65rem .9rem;cursor:pointer;font-weight:900;color:#1a1a2e}
  .pd-step input{width:70px;border:0;text-align:center;font-weight:800;padding:.6rem .2rem}
  .pd-cta{display:flex;gap:.8rem;margin-top:.5rem}
  .btn-gradient{background:linear-gradient(135deg,#f0ad4e 0%,#ff9800 100%);color:#fff !important;border:none;border-radius:12px;padding:.9rem 1.2rem;font-weight:900;box-shadow:0 8px 18px rgba(240,173,78,.28);transition:.2s;display:inline-flex;align-items:center;gap:.6rem;text-decoration:none}
  .btn-gradient:hover{transform:translateY(-2px);color:#fff}
  .btn-secondary-outline{border:2px solid #e5e7eb;border-radius:12px;padding:.85rem 1.1rem;font-weight:800;color:#1a1a2e;text-decoration:none}
  @media (max-width: 992px){.pd-grid{grid-template-columns:1fr}.pd-viewport{height:420px}.pd-info{position:static}}
</style>

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
            <button type="button" aria-label="Disminuir" onclick="pdDec()">−</button>
            <input id="cantidad" type="number" name="qty" min="1" max="{{ $bebida->stock }}" value="1">
            <button type="button" aria-label="Aumentar" onclick="pdInc()">+</button>
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
