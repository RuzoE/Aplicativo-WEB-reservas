{{-- resources/views/minibar/landing.blade.php --}}
@extends('layouts.app')

@section('header')
  @include('layouts.header')
@endsection

@section('content')
  {{-- Hero/Banner --}}
  @include('components.hero')

  {{-- 1) Categorías --}}
  <section class="popular-categories-section">
    <link rel="stylesheet" href="{{ asset('css/blade/minibar/landing--style1.css') }}">

    <div class="container">
      <div class="popular-categories-head">
        <h2 class="popular-categories-title">Categorías populares</h2>
        <p class="popular-categories-subtitle">Explora por tipo y encuentra tu bebida ideal en segundos.</p>
      </div>

      <div class="categories-grid">
        @forelse(($landingCategories ?? collect()) as $cat)
          @php
            $isNonAlcoholic = isset($cat->es_alcoholica) ? !$cat->es_alcoholica : false;
            $icon = $isNonAlcoholic ? 'fa-tint' : 'fa-wine-glass-alt';
            $tag = $isNonAlcoholic ? 'Sin alcohol' : 'Con alcohol';
          @endphp
          <a class="category-link" href="{{ route('minibar.catalogo', ['tipo' => $cat->id]) }}">
            <article class="category-card">
              <span class="category-tag">{{ $tag }}</span>
              <span class="category-icon"><i class="fas {{ $icon }}"></i></span>
              <h3 class="category-name">{{ $cat->nombre }}</h3>
              <p class="category-count">{{ $cat->products_count }} productos</p>
            </article>
          </a>
        @empty
          <article class="category-card">
            <span class="category-icon"><i class="fas fa-search"></i></span>
            <h3 class="category-name">Sin categorías activas</h3>
            <p class="category-count">Aún no hay productos cargados.</p>
          </article>
        @endforelse
      </div>
    </div>
  </section>

  {{-- 2) Bebidas destacadas --}}
  <section class="bg-light py-5">
    <link rel="stylesheet" href="{{ asset('css/blade/minibar/landing--style2.css') }}">
    <div class="container featured-section">
      <div class="featured-header">
        <h2 class="featured-title">Bebidas destacadas</h2>
        <p class="featured-subtitle">Descubre nuestra selección premium</p>
      </div>
      <div class="featured-grid">
        @foreach($featured as $p)
          <article class="featured-card">
            <div class="featured-img-wrap">
              <img src="{{ asset('storage/'.$p->imagen) }}" alt="{{ $p->nombre }}" loading="lazy">
              @if($p->stock > 0)
                <span class="featured-badge">Disponible</span>
              @endif
            </div>
            <div class="featured-body">
              <h3 class="featured-name">{{ $p->nombre }}</h3>
              <div class="featured-price">${{ number_format($p->precio, 2) }}</div>
              <a href="{{ route('minibar.bebida.show', $p) }}" class="featured-btn">Ver detalles</a>
            </div>
          </article>
        @endforeach
      </div>
      <div class="featured-link-wrap">
        <a href="{{ route('minibar.catalogo') }}" class="featured-link">
          Ver catálogo completo <span>→</span>
        </a>
      </div>
    </div>
  </section>
@endsection

@section('footer')
  @include('layouts.footer')
@endsection


