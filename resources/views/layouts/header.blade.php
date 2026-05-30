<div class="container-fluid bg-dark px-0" style="position: sticky; top: 0; z-index: 1000;">
    <!-- Debug styles: temporal - forzar visibilidad del navbar si está oculto por CSS/JS -->
    <style>
        .navbar-collapse{display:flex !important}
        .navbar-dark .navbar-nav .nav-link{color:#fff !important; visibility: visible !important; display: block !important; opacity: 1 !important}
        .d-flex.align-items-center.me-2.me-lg-3 {visibility: visible !important; display: flex !important; opacity: 1 !important}
        .d-flex.align-items-center.me-2.me-lg-3 .nav-link{visibility: visible !important; display: block !important; opacity: 1 !important}
        .dropdown-menu{visibility: visible !important; display: none !important}
        .dropdown-menu.show{display: block !important; visibility: visible !important}
        .dropdown-toggle::after{visibility: visible !important; opacity: 1 !important}
    </style>
    <div class="row gx-0">
        <div class="col-lg-3 bg-dark d-none d-lg-block">
            <a href="{{ route('home') }}"
               class="navbar-brand w-100 h-100 m-0 p-0 d-flex align-items-center justify-content-center">
                <h1 class="m-0 text-primary text-uppercase site-brand-title">Hotel Oasis</h1>
            </a>
        </div>

        <div class="col-lg-9">
            <nav class="navbar navbar-expand-lg bg-dark navbar-dark p-3 p-lg-0">
                <a href="{{ route('home') }}" class="navbar-brand d-block d-lg-none site-brand-mobile">
                    <h1 class="m-0 text-primary text-uppercase site-brand-title">Hotel Oasis</h1>
                </a>

                <button type="button" class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse justify-content-between" id="navbarCollapse">

                    {{-- Menú principal --}}
                    <div class="navbar-nav me-auto py-0">
                        <a class="nav-item nav-link {{ request()->routeIs('home') ? 'active' : '' }}"
                           href="{{ route('home') }}">Inicio</a>

                        <a class="nav-item nav-link {{ request()->routeIs('rooms.index') ? 'active' : '' }}"
                           href="{{ route('rooms.index') }}">Habitaciones</a>

                        <a class="nav-item nav-link {{ request()->routeIs('minibar.landing') ? 'active' : '' }}"
                           href="{{ route('minibar.landing') }}">Minibar</a>

                        @guest
                            <a class="nav-item nav-link {{ request()->routeIs('login') ? 'active' : '' }}"
                               href="{{ route('login') }}">Iniciar Sesión</a>

                            <a class="nav-item nav-link {{ request()->routeIs('register') ? 'active' : '' }}"
                               href="{{ route('register') }}">Registrarse</a>
                        @else
                            {{-- Usuario logueado: menú del perfil --}}
                            <div class="nav-item dropdown">
                                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                                    {{ Auth::user()->name }}
                                </a>
                                <div class="dropdown-menu rounded-0 m-0">
                                    <a href="{{ route('orders.index') }}" class="dropdown-item">Mis Reservas</a>
                                    <a href="{{ route('profile') }}" class="dropdown-item">Mi Perfil</a>
                                    <form method="post" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="btn btn-link dropdown-item">Salir</button>
                                    </form>
                                </div>
                            </div>

                            {{-- Botón Panel según el rol del usuario --}}
                            @hasanyrole('administrador|reservas|minibar|recepcion|mantenimiento')
                                @if(Auth::user()->hasRole('administrador'))
                                    <a href="{{ route('admin.index') }}"
                                       class="nav-item nav-link {{ request()->routeIs('admin.*') ? 'active' : '' }}">
                                        Panel
                                    </a>
                                @elseif(Auth::user()->hasRole('reservas'))
                                    <a href="{{ route('admin.habitaciones.dashboard') }}"
                                       class="nav-item nav-link {{ request()->routeIs('admin.habitaciones.*') ? 'active' : '' }}">
                                        Panel
                                    </a>
                                @elseif(Auth::user()->hasRole('minibar'))
                                    <a href="{{ route('admin.minibar.dashboard') }}"
                                       class="nav-item nav-link {{ request()->routeIs('admin.minibar.*') ? 'active' : '' }}">
                                        Panel
                                    </a>
                                @elseif(Auth::user()->hasRole('recepcion'))
                                    <a href="{{ route('reception.dashboard') }}"
                                       class="nav-item nav-link {{ request()->routeIs('reception.*') ? 'active' : '' }}">
                                        Panel
                                    </a>
                                @elseif(Auth::user()->hasRole('mantenimiento'))
                                    <a href="{{ route('admin.mantenimiento.dashboard') }}"
                                       class="nav-item nav-link {{ request()->routeIs('admin.mantenimiento.*') ? 'active' : '' }}">
                                        Panel
                                    </a>
                                @endif
                            @endhasanyrole
                        @endguest
                    </div>

                    {{-- Ícono carrito a la derecha --}}
                    <div class="d-flex align-items-center me-2 me-lg-3">
                        @auth
                            <a href="{{ route('minibar.carrito.index') }}" class="nav-link text-white">
                                <i class="fas fa-shopping-cart fa-lg"></i>
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="nav-link text-white">
                                <i class="fas fa-shopping-cart fa-lg"></i>
                            </a>
                        @endauth
                    </div>

                </div>
            </nav>
        </div>
    </div>
</div>
