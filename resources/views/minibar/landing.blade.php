{{-- resources/views/minibar/landing.blade.php --}}
@extends('layouts.app')

@section('header')
  @include('layouts.header')
@endsection

@section('content')
  {{-- Hero/Banner --}}
  @include('partials.hero')

  {{-- 1) Categorías --}}
  <section class="popular-categories-section">
    <style>
      .popular-categories-section {
        padding: 3.5rem 0 3rem;
        background:
          radial-gradient(circle at 85% -20%, rgba(240, 173, 78, 0.14), transparent 40%),
          radial-gradient(circle at 10% 120%, rgba(26, 36, 68, 0.12), transparent 35%),
          #f8fafc;
      }
      .popular-categories-head {
        text-align: center;
        margin-bottom: 2rem;
      }
      .popular-categories-title {
        font-size: clamp(1.8rem, 3.2vw, 2.6rem);
        font-weight: 900;
        letter-spacing: 0.2px;
        color: #101938;
        margin-bottom: 0.35rem;
      }
      .popular-categories-subtitle {
        color: #5e6a85;
        font-weight: 500;
        margin: 0;
      }
      .categories-grid {
        display: grid;
        grid-template-columns: repeat(1, minmax(0, 1fr));
        gap: 1rem;
      }
      .categories-grid.other {
        margin-top: 0.9rem;
      }
      @media (min-width: 576px) {
        .categories-grid {
          grid-template-columns: repeat(2, minmax(0, 1fr));
        }
      }
      @media (min-width: 992px) {
        .categories-grid {
          grid-template-columns: repeat(3, minmax(0, 1fr));
        }
      }
      .category-card {
        position: relative;
        background: #fff;
        border: 1px solid #e7ebf2;
        border-radius: 14px;
        padding: 1rem 1rem 1.1rem;
        min-height: 150px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        text-align: center;
        transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
      }
      .category-link {
        color: inherit;
        text-decoration: none;
        display: block;
      }
      .category-card:hover {
        transform: translateY(-6px);
        border-color: #f0ad4e;
        box-shadow: 0 12px 28px rgba(16, 25, 56, 0.14);
      }
      .category-icon {
        width: 44px;
        height: 44px;
        margin: 0 auto 0.7rem;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #fff2de 0%, #ffe3b8 100%);
        color: #d7830d;
        font-size: 1.25rem;
      }
      .category-name {
        font-size: 1.1rem;
        font-weight: 800;
        line-height: 1.2;
        color: #101938;
        margin: 0;
      }
      .category-count {
        margin-top: 0.4rem;
        font-size: 0.95rem;
        color: #6e7a93;
        font-weight: 600;
      }
      .category-tag {
        position: absolute;
        top: 10px;
        right: 10px;
        font-size: 0.68rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        padding: 0.2rem 0.45rem;
        border-radius: 999px;
        background: #eef2ff;
        color: #40507d;
      }
    </style>

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
    <style>
      .featured-section{background:linear-gradient(135deg,#f8f9fa 0%,#e9ecef 100%);padding:3.5rem 0}
      .featured-header{text-align:center;margin-bottom:2.5rem}
      .featured-title{font-size:2.5rem;font-weight:900;color:#1a1a2e;margin-bottom:.5rem}
      .featured-subtitle{color:#64748b;font-size:1.05rem;font-weight:500}
      .featured-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:1.75rem;margin-bottom:2.5rem}
      .featured-card{background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 4px 12px rgba(0,0,0,.06);transition:all .3s cubic-bezier(.4,0,.2,1);display:flex;flex-direction:column;height:100%}
      .featured-card:hover{transform:translateY(-8px);box-shadow:0 12px 28px rgba(240,173,78,.22)}
      .featured-img-wrap{position:relative;width:100%;height:260px;overflow:hidden;background:#f7f8fa}
      .featured-img-wrap img{width:100%;height:100%;object-fit:contain;transition:transform .4s ease}
      .featured-card:hover .featured-img-wrap img{transform:scale(1.08)}
      .featured-badge{position:absolute;top:12px;right:12px;background:linear-gradient(135deg,#f0ad4e 0%,#ff9800 100%);color:#fff;padding:.4rem .75rem;border-radius:999px;font-size:.75rem;font-weight:900;box-shadow:0 4px 10px rgba(240,173,78,.35)}
      .featured-body{padding:1.25rem;display:flex;flex-direction:column;flex:1}
      .featured-name{font-size:1.1rem;font-weight:800;color:#1a1a2e;margin-bottom:.75rem;line-height:1.3}
      .featured-price{font-size:1.75rem;font-weight:900;color:#f0ad4e;margin-top:auto;margin-bottom:.75rem}
      .featured-btn{display:inline-block;width:100%;background:linear-gradient(135deg,#f0ad4e 0%,#ff9800 100%);color:#fff;border:none;border-radius:10px;padding:.8rem 1rem;font-weight:800;text-align:center;text-decoration:none;transition:.2s;box-shadow:0 6px 14px rgba(240,173,78,.25)}
      .featured-btn:hover{transform:translateY(-2px);box-shadow:0 8px 18px rgba(240,173,78,.35);color:#fff}
      .featured-link-wrap{display:flex;justify-content:flex-end}
      .featured-link{color:#f0ad4e;font-weight:800;font-size:1.05rem;text-decoration:none;display:inline-flex;align-items:center;gap:.4rem;transition:.2s}
      .featured-link:hover{color:#ff9800;transform:translateX(4px)}
      @media (max-width:768px){.featured-title{font-size:2rem}.featured-grid{grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:1.25rem}}
    </style>
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
