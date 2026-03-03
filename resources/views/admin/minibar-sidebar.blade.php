<div class="col-auto col-md-3 col-xl-2 px-sm-2 px-0 bg-dark admin-sidebar-fixed">
  <div class="d-flex flex-column align-items-start w-100 px-3 pt-2 text-white min-vh-100">
    <a href="/" class="d-flex align-items-center pb-3 mb-md-0 me-md-auto text-white text-decoration-none">
      <span class="fs-5">Menu</span>
    </a>

    <div class="dropdown pb-4">
      <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
        <img src="https://github.com/mdo.png" alt="Admin" width="30" height="30" class="rounded-circle">
        <span class="d-none d-sm-inline mx-1">Admin</span>
      </a>
      <ul class="dropdown-menu dropdown-menu-dark text-small shadow">
        <li>
          <form method="post" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-link dropdown-item">Salir</button>
          </form>
        </li>
      </ul>
    </div>

    <ul class="nav nav-pills flex-column mb-sm-auto mb-0 align-items-start w-100" id="menu">
      <li class="nav-item">
        <a href="{{ route('home') }}" class="nav-link align-middle px-0 fs-5 pb-2">
          <i class="fs-4 bi-house"></i> <span class="ms-1">Inicio</span>
        </a>
      </li>

      <li class="nav-item">
        <a href="{{ route('admin.minibar.dashboard') }}" class="nav-link align-middle px-0 fs-5 pb-2">
          <i class="fs-4 bi-speedometer2"></i> <span class="ms-1">Dashboard</span>
        </a>
      </li>

      <li>
        <a href="{{ route('admin.minibar.ventas.index') }}" class="nav-link px-0 align-middle fs-5 pb-2">
          <i class="fs-4 bi-receipt"></i> <span class="ms-1">Ventas</span>
        </a>
      </li>

      <li>
        <a href="{{ route('admin.minibar.bebida-types.index') }}" class="nav-link px-0 align-middle fs-5 pb-2">
          <i class="fs-4 bi-bookmark-fill"></i> <span class="ms-1">Tipos Bebida</span>
        </a>
      </li>

      <li>
        <a href="{{ route('admin.minibar.bebidas.index') }}" class="nav-link px-0 align-middle fs-5 pb-2">
          <i class="fs-4 bi-cup-straw"></i> <span class="ms-1">Bebidas</span>
        </a>
      </li>

      <li class="nav-item mt-auto mb-2 pt-3 border-top w-100">
        <a href="{{ route('admin.index') }}" class="nav-link align-middle px-0 text-warning fs-5 pb-2">
          <i class="fs-4 bi-arrow-left-circle"></i> <span class="ms-1">Panel Principal</span>
        </a>
      </li>
    </ul>
  </div>
</div>
