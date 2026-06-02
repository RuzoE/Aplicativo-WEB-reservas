@extends('layouts.app')
@php $adminView = true; @endphp

@section('content')
@push('styles')
<link rel="stylesheet" href="{{ asset('css/usuarios/usuarios.css') }}">
<link rel="stylesheet" href="{{ asset('css/usuarios/dashboard.css') }}">
<link rel="stylesheet" href="{{ asset('css/usuarios/filtros.css') }}">
<link rel="stylesheet" href="{{ asset('css/usuarios/tablas.css') }}">
<link rel="stylesheet" href="{{ asset('css/empleados/index.css') }}?v={{ time() }}">
@endpush

<div class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 usuario-page">
  <div class="px-4 sm:px-6 lg:px-8 py-6">

    <!-- ========== HEADER PRINCIPAL ========== -->
    <div id="empleadosHeaderWrapper" class="relative overflow-hidden bg-white rounded-2xl border border-slate-100 shadow-sm p-6 md:p-8 mb-6 {{ $errors->any() ? 'd-none' : '' }}">
      <!-- Subtle ambient glows -->
      <div class="absolute top-0 right-0 w-64 h-64 bg-orange-50 rounded-full filter blur-3xl opacity-60 -mr-20 -mt-20 pointer-events-none"></div>
      <div class="absolute bottom-0 left-1/3 w-48 h-48 bg-blue-50 rounded-full filter blur-3xl opacity-40 pointer-events-none"></div>
      
      <div class="relative flex flex-col md:flex-row md:items-center justify-between gap-6">
        <!-- Main Info -->
        <div class="flex items-start gap-4">
          <div class="flex-shrink-0 flex items-center justify-center animated-header-icon header-icon-container">
            <i class="bi bi-people-fill text-2xl"></i>
          </div>
          <div>
            <div class="flex items-center gap-2 flex-wrap mb-1">
              <h1 class="text-2xl md:text-3xl font-extrabold text-slate-900 tracking-tight">Gestión de Empleados</h1>
              <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-orange-100 text-orange-700 border border-orange-200">
                {{ $empleados instanceof \Illuminate\Contracts\Pagination\Paginator ? $empleados->total() : count($empleados) }} Activos
              </span>
            </div>
            <p class="text-slate-500 text-sm md:text-base max-w-xl">
              Administra los empleados del hotel, gestiona sus roles operativos y controla los permisos de acceso al sistema.
            </p>
          </div>
        </div>
        
        <!-- Premium Action Button -->
        <div class="flex-shrink-0 flex items-center">
          <button type="button" id="btnNuevoEmpleado" class="btn-nuevo-empleado-premium">
            <i class="bi bi-person-plus-fill"></i>
            <span>Nuevo Empleado</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Formulario inline oculto -->
    <div id="empleadoFormWrapper" class="bg-white rounded-2xl shadow-xl border border-slate-100 mb-6 {{ $errors->any() ? '' : 'd-none' }} empleado-form-card overflow-hidden transition-all duration-300">
      <div class="bg-slate-50 border-b border-slate-100 px-6 py-4 flex justify-between items-center">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 bg-orange-100 text-orange-600 rounded-lg flex items-center justify-center text-lg">
            <i class="bi bi-person-plus-fill"></i>
          </div>
          <div>
            <h3 class="text-lg font-bold text-slate-800 m-0">Registrar Nuevo Empleado</h3>
            <p class="text-xs text-slate-500 m-0">Completa la información requerida</p>
          </div>
        </div>
        <button type="button" id="btnCerrarForm" class="btn-cancelar-form-premium">
          <i class="fas fa-times"></i>
          <span>Cancelar</span>
        </button>
      </div>
      <div class="p-6">
        <form action="{{ route('admin.empleados.store') }}" method="POST" autocomplete="off" id="empleadoCreateForm">
          @csrf

          <!-- Fila 1: Nombre, Apellido, Email -->
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div>
              <label class="block text-sm font-semibold text-slate-700 mb-2">Nombre *</label>
              <input type="text" name="name" class="w-full admin-input" placeholder="Ej: Carlos" value="{{ old('name') }}" required>
            </div>
            <div>
              <label class="block text-sm font-semibold text-slate-700 mb-2">Apellido *</label>
              <input type="text" name="last_name" class="w-full admin-input" placeholder="Ej: Pérez" value="{{ old('last_name') }}" required>
            </div>
            <div>
              <label class="block text-sm font-semibold text-slate-700 mb-2">Email *</label>
              <input type="email" name="email" class="w-full admin-input" placeholder="correo@example.com" pattern="^[^\s@]+@(gmail\.com|hotmail\.com)$" title="Solo se permiten correos @gmail.com o @hotmail.com" value="{{ old('email') }}" required>
            </div>
          </div>

          <!-- Fila 2: Teléfono, Contraseña, Confirmar -->
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div>
              <label class="block text-sm font-semibold text-slate-700 mb-2">Teléfono *</label>
              <input type="text" name="phone" class="w-full admin-input" placeholder="+57 3xx xxx xxxx" inputmode="tel" minlength="10" maxlength="16" pattern="^(3\d{9}|(?:\+57|57)3\d{9}|\+\d{8,15}|\d{8,15})$" value="{{ old('phone') }}" required>
            </div>
            <div>
              <label class="block text-sm font-semibold text-slate-700 mb-2">Contraseña *</label>
              <div class="flex">
                <input type="password" name="password" id="passwordInline" minlength="12" class="flex-1 admin-input rounded-r-none border-r-0" placeholder="••••••••" required>
                <button type="button" id="togglePassInline" class="px-3 border border-l-0 border-slate-200 bg-slate-50 text-slate-500 hover:bg-slate-100" title="Ver/ocultar"><i class="bi bi-eye"></i></button>
                <button type="button" id="genPassInline" class="px-3 border border-l-0 border-slate-200 bg-orange-50 text-orange-600 hover:bg-orange-100 rounded-r-xl font-medium" title="Generar"><i class="bi bi-shuffle mr-1"></i>Generar</button>
              </div>
              <div class="w-full bg-slate-200 h-1.5 mt-2 rounded-full overflow-hidden">
                <div id="passBarInline" class="bg-red-500 h-full w-0 transition-all duration-300"></div>
              </div>
            </div>
            <div>
              <label class="block text-sm font-semibold text-slate-700 mb-2">Confirmar *</label>
              <input type="password" name="password_confirmation" minlength="8" class="w-full admin-input" placeholder="••••••••" required>
            </div>
          </div>

          <!-- Acceso a Panel -->
          <div class="mb-6">
            <label class="block text-sm font-semibold text-slate-700 mb-3">Acceso a panel *</label>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              @foreach($rolesCreate as $id => $rname)
                <label class="flex items-center gap-4 p-4 border border-slate-200 rounded-xl cursor-pointer hover:border-orange-400 hover:bg-orange-50 transition-colors {{ old('role_id') == $id ? 'border-orange-500 bg-orange-50' : '' }}">
                  <input type="radio" name="role_id" value="{{ $id }}" class="w-5 h-5 text-orange-500 focus:ring-orange-500 border-slate-300" required @checked(old('role_id') == $id)>
                  <div>
                    <div class="font-bold text-slate-900 capitalize text-base">
                      @if($rname === 'reservas') <i class="bi bi-calendar-event text-amber-500 mr-2"></i>Reservas
                      @elseif($rname === 'recepcion') <i class="bi bi-person-badge text-blue-500 mr-2"></i>Recepción
                      @elseif($rname === 'mantenimiento') <i class="bi bi-tools text-purple-500 mr-2"></i>Mantenimiento
                      @else <i class="bi bi-shop text-green-500 mr-2"></i>Minibar
                      @endif
                    </div>
                  </div>
                </label>
              @endforeach
            </div>
          </div>

          <!-- Botones de Acción -->
          <div class="flex flex-wrap items-center gap-3 pt-6 border-t border-slate-100 mt-2">
            <button type="submit" class="inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-semibold rounded-xl shadow-md transition-colors">
              <i class="bi bi-check-circle"></i> Guardar empleado
            </button>
            <button type="button" class="inline-flex items-center justify-center px-6 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium rounded-xl transition-colors" id="btnResetEmpleado">
              Limpiar
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- ========== TABLA ========== -->
    <div id="empleadosTableWrapper" class="bg-white rounded-2xl shadow-lg border border-slate-100 overflow-hidden {{ $errors->any() ? 'd-none' : '' }}">
      <div class="overflow-x-auto usuarios-table-wrapper">
        <table class="w-full usuarios-table">
          <thead>
            <tr class="bg-gradient-to-r from-slate-50 to-slate-100 border-b-2 border-slate-200">
              <th class="px-4 py-3 text-left text-xs font-bold text-slate-700 uppercase tracking-wider whitespace-nowrap">ID</th>
              <th class="px-4 py-3 text-left text-xs font-bold text-slate-700 uppercase tracking-wider whitespace-nowrap">Nombre</th>
              <th class="px-4 py-3 text-left text-xs font-bold text-slate-700 uppercase tracking-wider whitespace-nowrap">Email</th>
              <th class="px-4 py-3 text-left text-xs font-bold text-slate-700 uppercase tracking-wider whitespace-nowrap">Rol actual</th>
              <th class="px-4 py-3 text-center text-xs font-bold text-slate-700 uppercase tracking-wider whitespace-nowrap" style="min-width:200px">Reasignar acceso</th>
              <th class="px-4 py-3 text-center text-xs font-bold text-slate-700 uppercase tracking-wider whitespace-nowrap">Acciones</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-200">
          @forelse($empleados as $u)
            <tr class="hover:bg-orange-50 transition-colors duration-200">
              <td class="px-3 py-2 text-sm text-slate-500 font-mono whitespace-nowrap">
                <span class="bg-slate-100 px-2 py-1 rounded-lg">{{ $u->id }}</span>
              </td>
              <td class="px-3 py-2 whitespace-nowrap">
                <div class="flex items-center gap-3">
                  <div>
                    <p class="font-semibold text-slate-900 m-0 leading-tight">{{ $u->name }} {{ $u->last_name }}</p>
                    <p class="text-xs text-slate-400 m-0">#{{ $u->id }}</p>
                  </div>
                </div>
              </td>
              <td class="px-3 py-2 text-sm text-slate-600 whitespace-nowrap">{{ $u->email }}</td>
              <td class="px-3 py-2 whitespace-nowrap">
                @php
                  $names = $u->roles->pluck('name');
                  $isAdmin = $u->hasRole('administrador');
                @endphp
                @if($names->isEmpty())
                  <span class="text-slate-400 text-sm">Sin rol</span>
                @else
                  @foreach($names as $r)
                    {!! match(strtolower($r)) {
                      'administrador' => '<span class="usuarios-badge bg-red-100 text-red-700 border border-red-300"><i class="fas fa-user-shield"></i>' . ucfirst($r) . '</span>',
                      'recepcion' => '<span class="usuarios-badge bg-blue-100 text-blue-700 border border-blue-300"><i class="fas fa-door-open"></i>' . ucfirst($r) . '</span>',
                      'reservas' => '<span class="usuarios-badge bg-amber-100 text-amber-700 border border-amber-300"><i class="fas fa-calendar"></i>' . ucfirst($r) . '</span>',
                      'mantenimiento' => '<span class="usuarios-badge bg-purple-100 text-purple-700 border border-purple-300"><i class="fas fa-wrench"></i>' . ucfirst($r) . '</span>',
                      'minibar' => '<span class="usuarios-badge bg-green-100 text-green-700 border border-green-300"><i class="fas fa-bottle-water"></i>' . ucfirst($r) . '</span>',
                      default => '<span class="usuarios-badge bg-slate-100 text-slate-700 border border-slate-300"><i class="fas fa-user"></i>' . ucfirst($r) . '</span>',
                    } !!}
                  @endforeach
                @endif
              </td>
              <td class="px-3 py-2 text-center whitespace-nowrap">
                @if($isAdmin)
                  <span class="text-slate-400 text-sm font-medium">—</span>
                @else
                  <form action="{{ route('admin.empleados.roles.assign', $u) }}" method="POST" class="inline-flex gap-2 align-items-center justify-center w-full">
                    @csrf
                    <select name="role" class="border-slate-200 rounded-lg text-sm py-1.5 focus:border-orange-500 focus:ring-0 w-32" required>
                      <option value="" disabled selected>Elegir…</option>
                      @foreach($roles as $rname)
                        <option value="{{ $rname }}">{{ ucfirst($rname) }}</option>
                      @endforeach
                    </select>
                    <button class="usuarios-action-btn bg-amber-100 hover:bg-amber-500 text-amber-600 hover:text-white" title="Reasignar" style="width: auto; padding: 0 0.75rem; border-radius: 0.5rem; height: 32px;">
                      <i class="fas fa-sync-alt mr-1"></i> Asignar
                    </button>
                  </form>
                @endif
              </td>
              <td class="px-3 py-2 text-center whitespace-nowrap">
                @if($isAdmin)
                  <span class="text-slate-400 text-sm font-medium">—</span>
                @else
                  @if(auth()->user()->hasRole('administrador') && $u->id !== auth()->id())
                    <form action="{{ route('admin.empleados.destroy', $u) }}" method="POST" class="inline-block" data-confirm-message="¿Estás seguro de eliminar a {{ $u->name }}?">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="usuarios-action-btn bg-red-100 hover:bg-red-500 text-red-600 hover:text-white" title="Eliminar empleado" style="border-radius: 0.5rem; height: 32px; width: 32px;">
                        <i class="fas fa-trash-alt"></i>
                      </button>
                    </form>
                  @else
                    <span class="text-slate-400 text-sm">—</span>
                  @endif
                @endif
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="px-6 py-16 text-center">
                <div class="flex flex-col items-center justify-center">
                  <i class="fas fa-users-slash text-6xl text-slate-300 mb-4"></i>
                  <p class="text-slate-500 text-lg font-semibold">No hay empleados registrados</p>
                </div>
              </td>
            </tr>
          @endforelse
          </tbody>
        </table>
      </div>
      @if ($empleados instanceof \Illuminate\Contracts\Pagination\Paginator)
        <div class="p-3 border-t border-slate-100 bg-slate-50 flex justify-center">
          {{ $empleados->links('pagination::tailwind') }}
        </div>
      @endif
    </div>

    @if ($errors->any())
      <div class="mt-4 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-lg">
        <ul class="mb-0 text-sm text-red-700 list-disc pl-5">
          @foreach ($errors->all() as $e)
            <li>{{ $e }}</li>
          @endforeach
        </ul>
      </div>
    @endif
  </div>
</div>

@push('scripts')
<script src="{{ asset('js/admin-empleados-form.js') }}?v={{ time() }}"></script>
<script src="{{ asset('js/empleados/index.js') }}?v={{ time() }}"></script>
@endpush
@endsection
