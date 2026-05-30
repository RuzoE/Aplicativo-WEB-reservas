@extends('layouts.app')

@section('content')
@push('styles')
<link rel="stylesheet" href="{{ asset('css/usuarios/usuarios.css') }}">
<link rel="stylesheet" href="{{ asset('css/usuarios/dashboard.css') }}">
<link rel="stylesheet" href="{{ asset('css/usuarios/filtros.css') }}">
<link rel="stylesheet" href="{{ asset('css/usuarios/tablas.css') }}">
@endpush
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 usuario-page">
  <div class="px-4 sm:px-6 lg:px-8 py-6">

    <!-- ========== HEADER PRINCIPAL ========== -->
    <section class="usuario-hero mb-5">
      <div class="usuario-hero-content">
        <div>
          <div class="flex flex-wrap gap-2 mb-4">
            <span class="usuario-pill" style="background: rgba(249, 115, 22, 0.15); color: #ea580c; border: 1px solid rgba(249, 115, 22, 0.3);">
              <i class="bi bi-shield-check"></i> Acceso Controlado
            </span>
            <span class="usuario-pill" style="background: rgba(34, 197, 94, 0.15); color: #16a34a; border: 1px solid rgba(34, 197, 94, 0.3);">
              <i class="bi bi-lock"></i> Seguridad
            </span>
            <span class="usuario-pill" style="background: rgba(59, 130, 246, 0.15); color: #2563eb; border: 1px solid rgba(59, 130, 246, 0.3);">
              <i class="bi bi-person-check"></i> Roles y Permisos
            </span>
          </div>
          <h1 class="usuario-hero-title">Gestión de Usuarios</h1>
          <p class="usuario-hero-description">
            Panel centralizado para administrar el acceso de empleados y clientes. Controla roles, permisos, seguridad y auditoría de todas las cuentas de usuario del sistema.
          </p>
        </div>
      </div>
    </section>

    <!-- ========== CARDS DE ESTADÍSTICAS ========== -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 mb-5 auto-rows-fr usuario-dashboard-grid">
      <!-- Total de usuarios -->
      <div class="usuario-stat-card border-blue-500">
        <div class="stat-head">
          <div>
            <p class="stat-title">Total Usuarios</p>
            <p class="stat-value">{{ $totalUsuarios ?? $usuarios->total() }}</p>
          </div>
          <div class="usuario-stat-icon bg-blue-100 group-hover:scale-110 transition-transform duration-300">
            <i class="fas fa-users text-blue-600 text-xl"></i>
          </div>
        </div>
        <p class="stat-description">Todos los registrados en el sistema</p>
      </div>

      <!-- Usuarios activos -->
      <div class="usuario-stat-card border-green-500">
        <div class="stat-head">
          <div>
            <p class="stat-title">Usuarios Activos</p>
            <p class="stat-value">{{ $usuariosActivos ?? 0 }}</p>
          </div>
          <div class="usuario-stat-icon bg-green-100 group-hover:scale-110 transition-transform duration-300">
            <i class="fas fa-check-circle text-green-600 text-xl"></i>
          </div>
        </div>
        <p class="stat-description">Con acceso permitido</p>
      </div>

      <!-- Usuarios inactivos -->
      <div class="usuario-stat-card border-red-500">
        <div class="stat-head">
          <div>
            <p class="stat-title">Usuarios Inactivos</p>
            <p class="stat-value">{{ $usuariosInactivos ?? 0 }}</p>
          </div>
          <div class="usuario-stat-icon bg-red-100 group-hover:scale-110 transition-transform duration-300">
            <i class="fas fa-times-circle text-red-600 text-xl"></i>
          </div>
        </div>
        <p class="stat-description">Acceso deshabilitado</p>
      </div>

      <!-- Usuarios con rol -->
      <div class="usuario-stat-card border-orange-500">
        <div class="stat-head">
          <div>
            <p class="stat-title">Con Rol Asignado</p>
            <p class="stat-value">{{ $usuariosConRol ?? 0 }}</p>
          </div>
          <div class="usuario-stat-icon bg-orange-100 group-hover:scale-110 transition-transform duration-300">
            <i class="fas fa-briefcase text-orange-600 text-xl"></i>
          </div>
        </div>
        <p class="stat-description">Roles de departamento</p>
      </div>
    </div>

    <!-- ========== BARRA DE FILTROS PREMIUM ========== -->
    <div class="bg-white rounded-2xl shadow-lg p-5 mb-5 border border-slate-100 usuarios-filters-panel">
      <div class="flex items-center gap-2 mb-4">
        <i class="fas fa-sliders-h text-orange-600 text-lg"></i>
        <h3 class="text-base font-semibold text-slate-900">Filtros Avanzados</h3>
      </div>

      <form method="GET" action="{{ route('admin.usuarios.index') }}" class="usuarios-filters-form">
        <div class="filter-row">
          <!-- Búsqueda -->
          <div class="filter-item filter-item--search">
            <label class="usuarios-filter-label" for="search-input">Buscar usuario</label>
            <div class="relative">
              <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm pointer-events-none"></i>
              <input id="search-input" type="text" name="search"
                placeholder="Nombre, email..."
                value="{{ request('search') }}"
                class="usuarios-filter-input w-full border-slate-200 focus:border-orange-500 focus:outline-none focus:ring-0 transition-colors duration-200 bg-white">
            </div>
          </div>

          <!-- Rol -->
          <div class="filter-item">
            <label class="usuarios-filter-label" for="role-select">Rol</label>
            <div class="relative">
              <select id="role-select" name="role" class="usuarios-filter-select w-full border-slate-200 focus:border-orange-500 focus:outline-none focus:ring-0 transition-colors duration-200 bg-white">
                <option value="">Todos los roles</option>
                @foreach($roles as $role)
                  <option value="{{ $role->name }}" {{ request('role') === $role->name ? 'selected' : '' }}>{{ ucfirst($role->name) }}</option>
                @endforeach
              </select>
              <i class="fas fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-sm"></i>
            </div>
          </div>

          <!-- Estado -->
          <div class="filter-item">
            <label class="usuarios-filter-label" for="status-select">Estado</label>
            <div class="relative">
              <select id="status-select" name="status" class="usuarios-filter-select w-full border-slate-200 focus:border-orange-500 focus:outline-none focus:ring-0 transition-colors duration-200 bg-white">
                <option value="">Todos los estados</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Activo</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactivo</option>
                <option value="blocked" {{ request('status') === 'blocked' ? 'selected' : '' }}>Bloqueado</option>
              </select>
              <i class="fas fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-sm"></i>
            </div>
          </div>

          <!-- Botones de acción -->
          <div class="filter-buttons">
            <button type="submit" class="usuarios-filter-actions-btn-filter" title="Aplicar filtros">
              <i class="fas fa-search"></i>
              <span>Filtrar</span>
            </button>
            <a href="{{ route('admin.usuarios.index') }}" class="usuarios-filter-actions-btn-clear" title="Limpiar filtros">
              <i class="fas fa-redo"></i>
              <span>Limpiar</span>
            </a>
          </div>
        </div>
      </form>
    </div>

    <!-- ========== TABLA MODERNA Y ELEGANTE ========== -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-slate-100">
      <div class="overflow-x-auto usuarios-table-wrapper">
        <table class="w-full usuarios-table">
          <thead>
            <tr class="bg-gradient-to-r from-slate-50 to-slate-100 border-b-2 border-slate-200">
              <th class="px-4 py-3 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">ID</th>
              <th class="px-4 py-3 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">Usuario</th>
              <th class="px-4 py-3 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">Email</th>
              <th class="px-4 py-3 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">Rol</th>
              <th class="px-4 py-3 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">Estado</th>
              <th class="px-4 py-3 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">Último acceso</th>
              <th class="px-4 py-3 text-center text-xs font-bold text-slate-700 uppercase tracking-wider">Acciones</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-200">
            @forelse($usuarios as $usuario)
              <tr class="hover:bg-orange-50 transition-colors duration-200 group">
                <td class="px-4 py-3 text-sm text-slate-500 font-mono">
                  <span class="bg-slate-100 px-3 py-1 rounded-lg">{{ $usuario->id }}</span>
                </td>
                <td class="px-4 py-3">
                  <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-orange-400 to-orange-600 rounded-full flex items-center justify-center text-white font-semibold text-sm shadow-md">
                      {{ substr($usuario->name, 0, 1) }}{{ substr($usuario->last_name ?? '', 0, 1) }}
                    </div>
                    <div>
                      <p class="font-semibold text-slate-900">{{ $usuario->name }} {{ $usuario->last_name }}</p>
                      @if($usuario->is_admin)
                        <span class="inline-block mt-1 px-2 py-1 bg-red-100 text-red-700 text-xs font-bold rounded-full">Admin</span>
                      @endif
                    </div>
                  </div>
                </td>
                <td class="px-4 py-3 text-sm text-slate-600">{{ $usuario->email }}</td>
                <td class="px-4 py-3">
                  {!! $usuario->display_role ? match(strtolower($usuario->display_role)) {
                    'administrador' => '<span class="usuarios-badge bg-red-100 text-red-700 border border-red-300"><i class="fas fa-user-shield"></i>' . ucfirst($usuario->display_role) . '</span>',
                    'recepcion' => '<span class="usuarios-badge bg-blue-100 text-blue-700 border border-blue-300"><i class="fas fa-door-open"></i>' . ucfirst($usuario->display_role) . '</span>',
                    'reservas' => '<span class="usuarios-badge bg-amber-100 text-amber-700 border border-amber-300"><i class="fas fa-calendar"></i>' . ucfirst($usuario->display_role) . '</span>',
                    'mantenimiento' => '<span class="usuarios-badge bg-purple-100 text-purple-700 border border-purple-300"><i class="fas fa-wrench"></i>' . ucfirst($usuario->display_role) . '</span>',
                    'minibar' => '<span class="usuarios-badge bg-green-100 text-green-700 border border-green-300"><i class="fas fa-bottle-water"></i>' . ucfirst($usuario->display_role) . '</span>',
                    default => '<span class="usuarios-badge bg-slate-100 text-slate-700 border border-slate-300"><i class="fas fa-user"></i>' . ucfirst($usuario->display_role) . '</span>',
                  } : '<span class="text-slate-400 text-sm">Sin rol</span>' !!}
                </td>
                <td class="px-4 py-3">
                  @if($usuario->status === 'active')
                    <span class="usuarios-state-badge bg-green-100 text-green-700 border border-green-300 rounded-full px-3 py-1.5">
                      <span class="inline-block w-2 h-2 bg-green-600 rounded-full animate-pulse"></span>
                      Activo
                    </span>
                  @elseif($usuario->status === 'blocked')
                    <span class="usuarios-state-badge bg-red-100 text-red-700 border border-red-300 rounded-full px-3 py-1.5">
                      <span class="inline-block w-2 h-2 bg-red-600 rounded-full animate-pulse"></span>
                      Bloqueado
                    </span>
                  @else
                    <span class="usuarios-state-badge bg-gray-100 text-gray-700 border border-gray-300 rounded-full px-3 py-1.5">
                      <span class="inline-block w-2 h-2 bg-gray-600 rounded-full animate-pulse"></span>
                      Inactivo
                    </span>
                  @endif
                </td>
                <td class="px-4 py-3 text-sm text-slate-600">
                  <div class="inline-flex items-center gap-2 text-slate-600">
                    <i class="fas fa-clock text-slate-400"></i>
                    <span>{{ $usuario->last_login_at ? $usuario->last_login_formatted : 'Nunca' }}</span>
                  </div>
                </td>
                <td class="px-4 py-3">
                  <div class="usuarios-actions-group">
                    <a href="{{ route('admin.usuarios.edit', $usuario) }}"
                      class="usuarios-action-btn bg-orange-100 hover:bg-orange-500 text-orange-600 hover:text-white"
                      title="Editar usuario">
                      <i class="fas fa-edit"></i>
                    </a>
                    <a href="{{ route('admin.usuarios.activity', $usuario) }}"
                      class="usuarios-action-btn bg-blue-100 hover:bg-blue-500 text-blue-600 hover:text-white"
                      title="Ver actividad">
                      <i class="fas fa-history"></i>
                    </a>
                    <a href="{{ route('admin.usuarios.sessions', $usuario) }}"
                      class="usuarios-action-btn bg-amber-100 hover:bg-amber-500 text-amber-600 hover:text-white"
                      title="Gestionar sesiones">
                      <i class="fas fa-wifi"></i>
                    </a>
                    <button type="button"
                      class="delete-btn usuarios-action-btn bg-red-100 hover:bg-red-500 text-red-600 hover:text-white"
                      data-id="{{ $usuario->id }}"
                      title="Eliminar usuario">
                      <i class="fas fa-trash-alt"></i>
                    </button>
                  </div>
                </td>
              </tr>


            @empty
              <tr>
                <td colspan="7" class="px-6 py-16 text-center">
                  <div class="flex flex-col items-center justify-center">
                    <i class="fas fa-inbox text-6xl text-slate-300 mb-4"></i>
                    <p class="text-slate-500 text-lg font-semibold">No hay usuarios registrados</p>
                    <p class="text-slate-400 text-sm mt-1">Los usuarios aparecerán aquí cuando se registren empleados o clientes</p>
                  </div>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    {{-- Contenedor móvil: tarjetas apiladas (se muestra solo en <768px) --}}
    <div class="usuarios-cards-mobile-container">
      @foreach($usuarios as $usuario)
        <div class="usuarios-card-mobile">
          <div class="card-top flex items-start justify-between">
            <div class="card-header-left">
              <p class="font-semibold text-slate-900 text-lg">{{ $usuario->name }} {{ $usuario->last_name }}</p>
              <div class="mt-1">
                {!! $usuario->display_role ? match(strtolower($usuario->display_role)) {
                  'administrador' => '<span class="usuarios-badge bg-red-100 text-red-700 border border-red-300"><i class="fas fa-user-shield"></i>' . ucfirst($usuario->display_role) . '</span>',
                  'recepcion' => '<span class="usuarios-badge bg-blue-100 text-blue-700 border border-blue-300"><i class="fas fa-door-open"></i>' . ucfirst($usuario->display_role) . '</span>',
                  'reservas' => '<span class="usuarios-badge bg-amber-100 text-amber-700 border border-amber-300"><i class="fas fa-calendar"></i>' . ucfirst($usuario->display_role) . '</span>',
                  'mantenimiento' => '<span class="usuarios-badge bg-purple-100 text-purple-700 border border-purple-300"><i class="fas fa-wrench"></i>' . ucfirst($usuario->display_role) . '</span>',
                  'minibar' => '<span class="usuarios-badge bg-green-100 text-green-700 border border-green-300"><i class="fas fa-bottle-water"></i>' . ucfirst($usuario->display_role) . '</span>',
                  default => '<span class="usuarios-badge bg-slate-100 text-slate-700 border border-slate-300"><i class="fas fa-user"></i>' . ucfirst($usuario->display_role) . '</span>',
                } : '<span class="text-slate-400 text-sm">Sin rol</span>' !!}
              </div>
            </div>
            <div class="card-actions-mobile">
              <a href="{{ route('admin.usuarios.edit', $usuario) }}" class="usuarios-action-btn bg-orange-100 hover:bg-orange-500 text-orange-600 hover:text-white" title="Editar usuario">
                <i class="fas fa-edit"></i>
              </a>
              <a href="{{ route('admin.usuarios.activity', $usuario) }}" class="usuarios-action-btn bg-blue-100 hover:bg-blue-500 text-blue-600 hover:text-white" title="Ver actividad">
                <i class="fas fa-history"></i>
              </a>
              <a href="{{ route('admin.usuarios.sessions', $usuario) }}" class="usuarios-action-btn bg-amber-100 hover:bg-amber-500 text-amber-600 hover:text-white" title="Gestionar sesiones">
                <i class="fas fa-wifi"></i>
              </a>
              <button type="button" class="delete-btn usuarios-action-btn bg-red-100 hover:bg-red-500 text-red-600 hover:text-white" data-id="{{ $usuario->id }}" title="Eliminar usuario">
                <i class="fas fa-trash-alt"></i>
              </button>
            </div>
          </div>

          <div class="card-body card-body--with-separator mt-3">
            <div class="card-data">
              <div class="label"><i class="fas fa-envelope"></i> Email</div>
              <div class="value">{{ $usuario->email }}</div>

              <div class="label"><i class="fas fa-user-check"></i> Estado</div>
              <div class="value">
                @if($usuario->status === 'active')
                  <span class="usuarios-state-badge bg-green-100 text-green-700 border border-green-300 rounded-full px-3 py-1.5">Activo</span>
                @elseif($usuario->status === 'blocked')
                  <span class="usuarios-state-badge bg-red-100 text-red-700 border border-red-300 rounded-full px-3 py-1.5">Bloqueado</span>
                @else
                  <span class="usuarios-state-badge bg-gray-100 text-gray-700 border border-gray-300 rounded-full px-3 py-1.5">Inactivo</span>
                @endif
              </div>

              <div class="label"><i class="fas fa-clock"></i> Último acceso</div>
              <div class="value">{{ $usuario->last_login_at ? $usuario->last_login_formatted : 'Nunca' }}</div>


            </div>
          </div>
        </div>
      @endforeach
    </div>

    <!-- ========== PAGINACIÓN MODERNA ========== -->
    @if($usuarios->hasPages())
    <div class="flex justify-center mb-8">
      <div class="inline-flex items-center gap-1 bg-white rounded-xl shadow-md p-2 border border-slate-100">
        {{ $usuarios->links('pagination::tailwind') }}
      </div>
    </div>
    @endif

    <!-- ========== ACTIVIDADES RECIENTES DEL SISTEMA ========== -->
    @if($recentActivities->count() > 0)
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-slate-100">
      <div class="bg-gradient-to-r from-slate-50 to-slate-100 border-b-2 border-slate-200 px-4 py-3 flex items-center gap-3">
        <i class="fas fa-history text-orange-600 text-lg"></i>
        <h3 class="text-lg font-bold text-slate-900">Actividades Recientes del Sistema</h3>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full">
          <thead>
            <tr class="bg-slate-50 border-b border-slate-200">
              <th class="px-6 py-3 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">Usuario</th>
              <th class="px-6 py-3 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">Acción</th>
              <th class="px-6 py-3 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">Descripción</th>
              <th class="px-6 py-3 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">Fecha y hora</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-200">
            @foreach($recentActivities as $activity)
              <tr class="hover:bg-orange-50 transition-colors duration-200">
                <td class="px-4 py-3 text-sm font-medium text-slate-900">
                  <div class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center text-white text-xs font-bold">
                      {{ substr($activity->user->name ?? 'S', 0, 1) }}
                    </div>
                    <span>{{ $activity->user->name ?? 'Sistema' }}</span>
                  </div>
                </td>
                <td class="px-4 py-3 text-sm">
                  <span class="inline-block px-3 py-1 bg-purple-100 text-purple-700 text-xs font-bold rounded-full uppercase">{{ str_replace('_', ' ', $activity->action) }}</span>
                </td>
                <td class="px-4 py-3 text-sm text-slate-600">{{ $activity->description }}</td>
                <td class="px-4 py-3 text-sm text-slate-500">
                  <div class="flex items-center gap-2">
                    <i class="fas fa-calendar-alt"></i>
                    <span>{{ $activity->created_at->diffForHumans() }}</span>
                  </div>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
  @endif

  </div>
</div>

<!-- Formulario oculto para eliminar -->
<form id="deleteForm" action="" method="POST" class="hidden">
  @csrf
  @method('DELETE')
</form>

<!-- Modal de confirmación elegante -->
<div id="deleteModal" class="hidden fixed inset-0 bg-black/50 z-50 items-center justify-center backdrop-blur-sm">
  <div class="bg-white rounded-2xl shadow-2xl p-6 w-full max-w-md mx-4 transform transition-all">
    <div class="flex items-start gap-4">
      <div class="p-3 bg-red-100 rounded-lg flex-shrink-0">
        <i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>
      </div>
      <div class="flex-1">
        <h3 class="text-xl font-bold text-slate-900 mb-2">Confirmar eliminación</h3>
        <p class="text-slate-600 text-sm">¿Estás seguro de que deseas eliminar este usuario? Esta acción no se puede deshacer.</p>
      </div>
    </div>
    <div class="flex gap-3 mt-6">
      <button id="cancelDelete" type="button" class="flex-1 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-lg transition-colors duration-200">
        Cancelar
      </button>
      <button id="confirmDelete" type="button" class="flex-1 px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg transition-colors duration-200">
        Eliminar
      </button>
    </div>
  </div>
</div>

@push('scripts')
<script src="{{ asset('js/usuarios/tabla.js') }}"></script>
@endpush
@endsection
