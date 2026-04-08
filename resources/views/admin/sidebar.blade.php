<div class="col-auto col-md-3 col-xl-2 px-sm-2 px-0 bg-dark admin-sidebar-fixed">
  <div class="d-flex flex-column align-items-start w-100 px-3 pt-2 text-white min-vh-100">
    <a href="/" class="d-flex align-items-center pb-3 mb-md-0 me-md-auto text-white text-decoration-none">
      <span class="fs-5">Menú</span>
    </a>

    <link rel="stylesheet" href="{{ asset('css/blade/admin/sidebar--style1.css') }}">

    <div class="dropdown pb-4">
      <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
        <img src="https://github.com/mdo.png" alt="hugenerd" width="30" height="30" class="rounded-circle">
        <span class="d-none d-sm-inline mx-1">Administrador</span>
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

      @if((auth()->user()->hasRole('administrador') || auth()->user()->hasRole('reservas')) && !request()->routeIs('reception.*'))
      <li>
        <a href="{{ route('admin.habitaciones.reservas.index') }}" class="nav-link px-0 align-middle fs-5 pb-2">
          <i class="fs-4 bi-speedometer"></i> <span class="ms-1">{{ __('messages.dashboard_reservations') }}</span>
        </a>
      </li>
      @endif

      @if((auth()->user()->hasRole('administrador') || auth()->user()->hasRole('minibar')) && !request()->routeIs('reception.*'))
      <li>
        <a href="{{ route('admin.minibar.ventas.index') }}" class="nav-link px-0 align-middle fs-5 pb-2">
          <i class="fs-4 bi-bar-chart"></i> <span class="ms-1">{{ __('messages.dashboard_minibar') }}</span>
        </a>
      </li>
      @endif

      @if((auth()->user()->hasRole('administrador') || auth()->user()->hasRole('mantenimiento')) && !request()->routeIs('reception.*'))
            <li>
                <a href="{{ route('admin.mantenimiento.dashboard') }}" class="nav-link px-0 align-middle fs-5 pb-2">
                    <i class="fs-4 bi-tools"></i> <span class="ms-1">Mantenimiento</span>
                </a>
            </li>
      @endif

      @if(auth()->user()->hasRole('administrador') || auth()->user()->hasRole('recepcion'))
      <li class="nav-item mb-2">
        <a href="{{ route('reception.dashboard') }}" class="nav-link px-0 align-middle fs-5 pb-2 {{ request()->routeIs('reception.dashboard') && !request()->hasHeader('referer') ? 'active text-warning' : '' }}">
          <i class="fs-4 bi-grid-3x3-gap-fill"></i>
          <span class="ms-1">{{ __('messages.dashboard_reception') }}</span>
        </a>
      </li>

      {{-- Solo mostrar los sub-módulos específicos cuando estamos en el panel de recepción --}}
      @if(request()->routeIs('reception.*'))
      <li class="nav-item mb-2">
        <a href="{{ route('reception.anticipos.index') }}" class="nav-link px-0 align-middle fs-5 pb-2 {{ request()->routeIs('reception.anticipos.*') ? 'active text-warning' : '' }}">
          <i class="fs-4 bi bi-cash-stack"></i>
          <span class="ms-1">Anticipos</span>
          @if($anticiposCount > 0)
            <span class="badge bg-warning text-dark ms-1 shadow-sm">{{ $anticiposCount }}</span>
          @endif
        </a>
      </li>

      <li class="nav-item mb-2">
        <a href="{{ route('reception.asignacion.index') }}" class="nav-link px-0 align-middle fs-5 pb-2 {{ request()->routeIs('reception.asignacion.*') ? 'active text-warning' : '' }}">
          <i class="fs-4 bi bi-building"></i>
          <span class="ms-1">Asignar Habitación</span>
        </a>
      </li>

      <li class="nav-item mb-2">
        <a href="{{ route('reception.dashboard') }}#checkin" class="nav-link px-0 align-middle fs-5 pb-2">
          <i class="fs-4 bi-door-open-fill"></i>
          <span class="ms-1">Check-in</span>
        </a>
      </li>

      <li class="nav-item mb-2">
        <a href="{{ route('reception.dashboard') }}#userlink" class="nav-link px-0 align-middle fs-5 pb-2">
          <i class="fs-4 bi-person-plus-fill"></i>
          <span class="ms-1">Asociar Cuentas</span>
        </a>
      </li>

      <li class="nav-item mb-2">
        <a href="{{ route('reception.dashboard') }}#folio" class="nav-link px-0 align-middle fs-5 pb-2">
          <i class="fs-4 bi-receipt"></i>
          <span class="ms-1">Folio</span>
        </a>
      </li>

      <li class="nav-item mb-2">
        <a href="{{ route('reception.dashboard') }}#checkout" class="nav-link px-0 align-middle fs-5 pb-2">
          <i class="fs-4 bi-box-arrow-in-right"></i>
          <span class="ms-1">Check-out</span>
        </a>
      </li>
      @endif
      @endif

      {{-- Gestión de empleados - solo para administradores --}}
      @if(auth()->user()->hasRole('administrador') && !request()->routeIs('reception.*'))
      <li>
        <a href="{{ route('admin.empleados.index') }}" class="nav-link px-0 align-middle fs-5 pb-2 {{ request()->routeIs('admin.empleados.*') ? 'active' : '' }}">
          <i class="fs-4 bi-people"></i> <span class="ms-1">Empleados</span>
        </a>
      </li>
      <li>
        <a href="{{ route('admin.report.preview') }}" class="nav-link px-0 align-middle fs-5 pb-2 {{ request()->routeIs('admin.report.preview') ? 'active' : '' }}">
          <i class="fs-4 bi-file-earmark-bar-graph"></i> <span class="ms-1">Informe General</span>
        </a>
      </li>
      <li>
        <a href="{{ route('admin.auditorias.index') }}" class="nav-link px-0 align-middle fs-5 pb-2 {{ request()->routeIs('admin.auditorias.*') ? 'active' : '' }}">
          <i class="fs-4 bi-shield-check"></i> <span class="ms-1">Auditoria</span>
        </a>
      </li>
      <li>
        <a href="{{ route('admin.backups.index') }}" class="nav-link px-0 align-middle fs-5 pb-2 {{ request()->routeIs('admin.backups.*') ? 'active' : '' }}">
          <i class="fs-4 bi-cloud-arrow-up"></i> <span class="ms-1">Backups</span>
        </a>
      </li>
      @endif

      @if(auth()->user()->hasRole('administrador') && !request()->routeIs('admin.index'))
      <li class="nav-item mt-auto mb-2 pt-3 border-top w-100">
        <a href="{{ route('admin.index') }}" class="nav-link align-middle px-0 fs-5 pb-2">
          <i class="fs-4 bi-arrow-left-circle"></i> <span class="ms-1">{{ __('messages.main_panel') }}</span>
        </a>
      </li>
      @endif
    </ul>
  </div>
</div>


