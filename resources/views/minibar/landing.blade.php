{{-- resources/views/minibar/landing.blade.php --}}
@extends('layouts.app')

@section('header')
  @include('layouts.header')
@endsection

@section('content')
  {{-- Hero/Banner --}}
  @include('partials.hero')

  {{-- 1) Categorías --}}
  <section class="py-5">
    <div class="container">
      <h2 class="h3 mb-4 text-center">Categorías populares</h2>
      <div class="row g-3 justify-content-center">
        @foreach($categories as $cat)
          <div class="col-6 col-sm-4 col-md-3 col-lg-2">
            <div
              class="card h-100 text-center shadow-sm"
              style="transition: transform .3s ease, box-shadow .3s ease;"
              onmouseover="this.classList.replace('shadow-sm','shadow-lg'); this.style.transform='translateY(-5px)';"
              onmouseout="this.classList.replace('shadow-lg','shadow-sm'); this.style.transform='none';"
            >
              <div class="card-body d-flex flex-column justify-content-center">
                <i class="fas fa-glass-alt fa-2x mb-2 text-primary"></i>
                <h5 class="card-title mb-1">{{ $cat->nombre }}</h5>
                <p class="small text-muted">{{ $cat->products_count }} productos</p>
              </div>
            </div>
          </div>
        @endforeach
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
