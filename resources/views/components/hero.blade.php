{{-- resources/views/minibar/partials/hero.blade.php --}}
<section class="position-relative" style="margin: 0; padding: 0; width: 100%;">
  <div id="minibarCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="5000" style="margin: 0; padding: 0;">
    <div class="carousel-inner">
      @php
        $slides = [
          [
            'img'         => 'bar 1.jpg',
            'smallText'   => 'PRUEBA LO MEJOR DE NUESTRA CARTA DE MIXOLOGÍA',
            'title'       => 'Descubre nuestros cócteles',
            'searchLabel' => 'Buscar bebidas…'
          ],
          [
            'img'         => 'bar 2.webp',
            'smallText'   => 'TU DESTINO PARA LAS MEJORES BEBIDAS Y CÓCTELES',
            'title'       => 'Bienvenidos al MiniBar',
            'searchLabel' => 'Buscar bebidas…'
          ],
        ];
      @endphp

      @foreach($slides as $i => $slide)
        <div class="carousel-item @if($i === 0) active @endif">
          <div
            class="w-100"
            style="
              background-image: url('{{ asset('img/'.$slide['img']) }}');
              height: 450px;
              background-size: cover;
              background-position: center;
            ">
            <div
              class="carousel-caption d-flex flex-column justify-content-center align-items-center h-100 p-0"
              style="background: rgba(0,0,0,0.4);">

              {{-- Texto pequeño con barras amarillas --}}
              <div class="d-flex align-items-center mb-3">
                <span class="border-top border-warning flex-grow-1 mx-3"></span>
                <small class="fw-bold text-white text-uppercase">{{ $slide['smallText'] }}</small>
                <span class="border-top border-warning flex-grow-1 mx-3"></span>
              </div>

              {{-- Título principal --}}
              <h1 class="display-4 fw-bold text-white mb-4">{{ $slide['title'] }}</h1>

              {{-- Buscador --}}
              <form class="d-flex justify-content-center" method="GET" action="{{ route('minibar.catalogo') }}">
                <div class="input-group" style="max-width: 500px;">
                  <input type="search" name="q" class="form-control" placeholder="{{ $slide['searchLabel'] }}">
                  <button class="btn btn-warning" type="submit">
                    <i class="fas fa-search"></i>
                  </button>
                </div>
              </form>

            </div>
          </div>
        </div>
      @endforeach
    </div>

    {{-- Controles de navegación --}}
    <button class="carousel-control-prev" type="button" data-bs-target="#minibarCarousel" data-bs-slide="prev">
      <span class="carousel-control-prev-icon" aria-hidden="true"></span>
      <span class="visually-hidden">Anterior</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#minibarCarousel" data-bs-slide="next">
      <span class="carousel-control-next-icon" aria-hidden="true"></span>
      <span class="visually-hidden">Siguiente</span>
    </button>
  </div>
</section>
