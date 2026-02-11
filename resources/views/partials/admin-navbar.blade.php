{{-- resources/views/partials/admin-navbar.blade.php --}}
<div class="col-auto col-md-3 col-xl-2 px-sm-2 px-0 bg-dark">
  <div class="d-flex flex-column align-items-center align-items-sm-start text-white min-vh-100">

    {{-- Título --}}
    <a href="#" class="d-flex align-items-center pb-3 mb-md-0 me-md-auto text-white text-decoration-none">
      <span class="fs-5 d-none d-sm-inline">Menu</span>
    </a>
    <hr class="border-secondary w-100 my-0">

    {{-- Usuario (avatar + dropdown) --}}
    <div class="dropdown py-4 w-100 text-center">
      <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle"
         id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
        {{-- Aquí la URL fija de GitHub --}}
        <img src="https://github.com/mdo.png"
             alt="Admin"
             width="32" height="32"
             class="rounded-circle me-2">
        <span class="d-none d-sm-inline">{{ Auth::user()->name }}</span>
      </a>
      <ul class="dropdown-menu dropdown-menu-dark text-small shadow">
        <li>
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="dropdown-item">Salir</button>
          </form>
        </li>
      </ul>
    </div>
    <hr class="border-secondary w-100 my-0">

    {{-- Menú de navegación --}}
    <ul class="nav nav-pills flex-column mb-sm-auto mb-0 align-items-center align-items-sm-start mt-3 w-100" id="menu">
      <li class="nav-item w-100">
        <a href="{{ route('home') }}"
            class="nav-link text-white px-3">
            <i class="bi bi-house-fill fs-4"></i>
            <span class="ms-2 d-none d-sm-inline">Inicio</span>
        </a>
      </li>
      <li class="nav-item w-100">
        <a href="{{ route('admin.minibar.dashboard') }}"
           class="nav-link text-white px-3 {{ request()->routeIs('admin.minibar.dashboard') ? 'active' : '' }}">
          <i class="bi bi-speedometer2 fs-4"></i>
          <span class="ms-2 d-none d-sm-inline">Dashboard</span>
        </a>
      </li>
      <li class="nav-item w-100">
        <a href="{{ route('admin.minibar.ventas.index') }}"
           class="nav-link text-white px-3 {{ request()->routeIs('admin.minibar.ventas.*') ? 'active' : '' }}">
          <i class="bi bi-receipt-cutoff fs-4"></i>
          <span class="ms-2 d-none d-sm-inline">Ventas</span>
        </a>
      </li>
      <li class="nav-item w-100">
        <a href="{{ route('admin.minibar.bebida-types.index') }}"
           class="nav-link text-white px-3 {{ request()->routeIs('admin.minibar.bebida-types.*') ? 'active' : '' }}">
          <i class="bi bi-grid-1x2-fill fs-4"></i>
          <span class="ms-2 d-none d-sm-inline">Tipos Bebida</span>
        </a>
      </li>
      <li class="nav-item w-100">
        <a href="{{ route('admin.minibar.bebidas.index') }}"
           class="nav-link text-white px-3 {{ request()->routeIs('admin.minibar.bebidas.*') ? 'active' : '' }}">
          <i class="bi bi-cup-straw fs-4"></i>
          <span class="ms-2 d-none d-sm-inline">Bebidas</span>
        </a>
      </li>
    </ul>
  </div>
</div>
