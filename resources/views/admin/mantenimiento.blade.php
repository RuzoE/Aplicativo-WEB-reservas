@extends('layouts.app')

@section('content')
<div class="d-flex">
    <!-- Sidebar -->
    <div id="sidebar" class="bg-dark text-white admin-sidebar-fixed">
        <div class="d-flex flex-column align-items-start w-100 px-3 pt-2 text-white min-vh-100">
            <a href="/" class="d-flex align-items-center pb-3 mb-md-0 me-md-auto text-white text-decoration-none">
                <span class="fs-5">Menú</span>
            </a>

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
                @if(auth()->user()->hasRole('administrador'))
                <li class="nav-item">
                    <a href="{{ route('admin.dashboard') }}" class="nav-link align-middle px-0 fs-5 pb-2">
                        <i class="fs-4 bi-arrow-left"></i> <span class="ms-1">Panel Principal</span>
                    </a>
                </li>
                @endif
                <li class="nav-item">
                    <a href="{{ route('admin.mantenimiento.create') }}" class="nav-link align-middle px-0 fs-5 pb-2">
                        <i class="fs-4 bi-plus-circle"></i> <span class="ms-1">Crear Orden</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.mantenimiento.history') }}" class="nav-link align-middle px-0 fs-5 pb-2">
                        <i class="fs-4 bi-clock-history"></i> <span class="ms-1">Historial</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.mantenimiento.settings') }}" class="nav-link align-middle px-0 fs-5 pb-2">
                        <i class="fs-4 bi-gear"></i> <span class="ms-1">Configuración</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Main Content -->
    <div id="main-content" class="col py-3">
        <div class="container-fluid">
            <h1>Bienvenido al módulo de mantenimiento</h1>
            <p>Selecciona una opción del menú para continuar.</p>
        </div>
    </div>
</div>
@endsection
