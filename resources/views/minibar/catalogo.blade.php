@extends('layouts.app')

@section('header')
  @include('layouts.header')
@endsection

@section('content')
<link rel="stylesheet" href="{{ asset('css/blade/minibar/catalogo--style1.css') }}">

<div class="catalog-container">
  <a href="{{ route('minibar.landing') }}" class="back-link">
    <i class="fas fa-arrow-left"></i> Volver al inicio
  </a>

  <div class="catalog-header">
    <h1 style="margin:0;font-size:2rem;font-weight:900;color:#1a1a2e">Catálogo de Bebidas</h1>
  </div>

  <form id="catalog-form" class="search-filter" method="GET" action="{{ route('minibar.catalogo') }}">
    <input class="search-input" type="search" name="q" value="{{ request('q') }}" placeholder="Buscar bebidas por nombre...">
    <select class="filter-select" name="tipo" id="tipo-filter">
      <option value="">— Todos los tipos —</option>
      @foreach($categories as $cat)
        <option value="{{ $cat->id }}" @selected(request('tipo') == $cat->id)>{{ $cat->nombre }}</option>
      @endforeach
    </select>
    <button class="search-btn" type="submit"><i class="fas fa-search"></i></button>
  </form>

  @if($products->count() === 0)
    <div class="products-grid">
      <div class="empty">
        <div style="font-size:3rem;opacity:.25;margin-bottom:.5rem"><i class="fas fa-search"></i></div>
        <div>No se encontraron bebidas para estos filtros.</div>
        <div style="margin-top:1rem"><a class="product-btn" style="display:inline-flex" href="{{ route('minibar.catalogo') }}"><i class="fas fa-redo"></i>&nbsp;Limpiar filtros</a></div>
      </div>
    </div>
  @else
    @if($showNonAlcoholicSection)
      <div class="products-section">
        <div class="section-header-wrap">
          <h2 class="section-title">Bebidas No Alcohólicas</h2>
          <p class="section-subtitle">Refrescantes y ligeras para cualquier momento.</p>
        </div>
        <div class="products-grid">
          @forelse($nonAlcoholicProducts as $p)
            <div class="product-card">
              <div class="product-image">
                <img src="{{ $p->image_url }}" alt="{{ $p->nombre }}">
              </div>
              <div class="product-body">
                <div class="product-category">{{ $p->type->nombre ?? 'Bebida' }}</div>
                <div class="product-name">{{ $p->nombre }}</div>
                @if($p->stock <= 0)
                  <div class="product-stock product-stock--out">Agotado</div>
                @elseif($p->stock <= 5)
                  <div class="product-stock product-stock--low">Stock bajo: {{ $p->stock }}</div>
                @else
                  <div class="product-stock">Stock: {{ $p->stock }}</div>
                @endif
                <div class="product-price">${{ number_format($p->precio, 2) }}</div>
                <a class="product-btn" href="{{ route('minibar.bebida.show', $p) }}"><i class="fas fa-eye"></i> Ver detalles</a>
              </div>
            </div>
          @empty
            <div class="empty">No hay bebidas no alcohólicas en esta página.</div>
          @endforelse
        </div>
      </div>
    @endif

    @if($showAlcoholicSection)
      <div class="products-section">
        <div class="section-header-wrap">
          <h2 class="section-title">Bebidas Alcohólicas</h2>
          <p class="section-subtitle">Selección para quienes buscan más intensidad y sabor.</p>
        </div>
        <div class="products-grid">
          @forelse($alcoholicProducts as $p)
            <div class="product-card">
              <div class="product-image">
                <img src="{{ $p->image_url }}" alt="{{ $p->nombre }}">
              </div>
              <div class="product-body">
                <div class="product-category">{{ $p->type->nombre ?? 'Bebida' }}</div>
                <div class="product-name">{{ $p->nombre }}</div>
                @if($p->stock <= 0)
                  <div class="product-stock product-stock--out">Agotado</div>
                @elseif($p->stock <= 5)
                  <div class="product-stock product-stock--low">Stock bajo: {{ $p->stock }}</div>
                @else
                  <div class="product-stock">Stock: {{ $p->stock }}</div>
                @endif
                <div class="product-price">${{ number_format($p->precio, 2) }}</div>
                <a class="product-btn" href="{{ route('minibar.bebida.show', $p) }}"><i class="fas fa-eye"></i> Ver detalles</a>
              </div>
            </div>
          @empty
            <div class="empty">No hay bebidas alcohólicas en esta página.</div>
          @endforelse
        </div>
      </div>
    @endif
  @endif

  @if($products->hasPages())
    <div class="pagination">{{ $products->withQueryString()->links() }}</div>
  @endif
</div>

<script src="{{ asset('js/minibar-catalogo.js') }}"></script>
@endsection

@section('footer')
  @include('layouts.footer')
@endsection


