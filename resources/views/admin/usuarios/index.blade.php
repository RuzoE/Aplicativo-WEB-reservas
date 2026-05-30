@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100">
  <div class="px-4 sm:px-6 lg:px-8 py-8">
    
    <!-- ========== HEADER PRINCIPAL ========== -->
    <div class="mb-8">
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-6">
        <div>
          <div class="flex items-center gap-4 mb-3">
            <div class="p-3 bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl shadow-lg">
              <i class="fas fa-shield-alt text-white text-2xl"></i>
            </div>
            <div>
              <h1 class="text-3xl md:text-4xl font-bold text-slate-900">Gestión de Usuarios</h1>
              <p class="text-slate-600 mt-1">Panel de administración de acceso y seguridad</p>
            </div>
          </div>
          <p class="text-slate-500 text-sm ml-16">Los usuarios se crean automáticamente al registrar empleados o clientes. Aquí puedes gestionar su acceso y seguridad.</p>
        </div>
        
        <!-- Card de estadística destacada -->
        <div class="bg-white rounded-2xl shadow-lg p-4 border-l-4 border-orange-500 hover:shadow-xl transition-all duration-300 flex-shrink-0 h-full flex flex-col justify-between">
          <p class="text-slate-600 text-sm font-semibold uppercase tracking-wide">Total de Usuarios</p>
          <div class="flex items-center gap-2 mt-3">
            <span class="text-4xl font-bold text-orange-600">{{ $totalUsuarios ?? $usuarios->total() }}</span>
            <i class="fas fa-users text-orange-500 text-2xl"></i>
          </div>
        </div>
      </div>
    </div>

    <!-- ========== CARDS DE ESTADÍSTICAS ========== -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mb-6 auto-rows-fr">
      <!-- Total de usuarios -->
      <div class="bg-white rounded-xl shadow-md hover:shadow-lg transition-all duration-300 overflow-hidden border-t-4 border-blue-500 p-4 group h-full flex flex-col justify-between">
        <div class="flex items-center justify-between mb-3">
          <div>
            <p class="text-slate-600 text-xs font-semibold uppercase tracking-wider">Total Usuarios</p>
            <p class="text-3xl font-bold text-slate-900 mt-2">{{ $totalUsuarios ?? $usuarios->total() }}</p>
          </div>
          <div class="w-12 h-12 flex items-center justify-center bg-blue-100 rounded-lg group-hover:scale-110 transition-transform duration-300">
            <i class="fas fa-users text-blue-600 text-xl"></i>
          </div>
        </div>
        <p class="text-xs text-slate-500">Todos los registrados en el sistema</p>
      </div>

      <!-- Usuarios activos -->
      <div class="bg-white rounded-xl shadow-md hover:shadow-lg transition-all duration-300 overflow-hidden border-t-4 border-green-500 p-4 group h-full flex flex-col justify-between">
        <div class="flex items-center justify-between mb-3">
          <div>
            <p class="text-slate-600 text-xs font-semibold uppercase tracking-wider">Usuarios Activos</p>
            <p class="text-3xl font-bold text-slate-900 mt-2">{{ $usuariosActivos ?? 0 }}</p>
          </div>
          <div class="w-12 h-12 flex items-center justify-center bg-green-100 rounded-lg group-hover:scale-110 transition-transform duration-300">
            <i class="fas fa-check-circle text-green-600 text-xl"></i>
          </div>
        </div>
        <p class="text-xs text-slate-500">Con acceso permitido</p>
      </div>

      <!-- Usuarios inactivos -->
      <div class="bg-white rounded-xl shadow-md hover:shadow-lg transition-all duration-300 overflow-hidden border-t-4 border-red-500 p-4 group h-full flex flex-col justify-between">
        <div class="flex items-center justify-between mb-3">
          <div>
            <p class="text-slate-600 text-xs font-semibold uppercase tracking-wider">Usuarios Inactivos</p>
            <p class="text-3xl font-bold text-slate-900 mt-2">{{ $usuariosInactivos ?? 0 }}</p>
          </div>
          <div class="w-12 h-12 flex items-center justify-center bg-red-100 rounded-lg group-hover:scale-110 transition-transform duration-300">
            <i class="fas fa-times-circle text-red-600 text-xl"></i>
          </div>
        </div>
        <p class="text-xs text-slate-500">Acceso deshabilitado</p>
      </div>

      <!-- Usuarios con rol -->
      <div class="bg-white rounded-xl shadow-md hover:shadow-lg transition-all duration-300 overflow-hidden border-t-4 border-orange-500 p-4 group h-full flex flex-col justify-between">
        <div class="flex items-center justify-between mb-3">
          <div>
            <p class="text-slate-600 text-xs font-semibold uppercase tracking-wider">Con Rol Asignado</p>
            <p class="text-3xl font-bold text-slate-900 mt-2">{{ $usuariosConRol ?? 0 }}</p>
          </div>
          <div class="w-12 h-12 flex items-center justify-center bg-orange-100 rounded-lg group-hover:scale-110 transition-transform duration-300">
            <i class="fas fa-briefcase text-orange-600 text-xl"></i>
          </div>
        </div>
        <p class="text-xs text-slate-500">Roles de departamento</p>
      </div>
    </div>

    <!-- ========== BARRA DE FILTROS PREMIUM ========== -->
    <div class="bg-white rounded-2xl shadow-lg p-6 md:p-8 mb-8 border border-slate-100">
      <div class="flex items-center gap-2 mb-6">
        <i class="fas fa-sliders-h text-orange-600 text-lg"></i>
        <h3 class="text-lg font-semibold text-slate-900">Filtros Avanzados</h3>
      </div>
      
      <form method="GET" action="{{ route('admin.usuarios.index') }}" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
          <!-- Búsqueda -->
          <div class="relative group md:col-span-2 lg:col-span-2">
            <label class="block text-sm font-medium text-slate-800 mb-2">Buscar usuario</label>
            <div class="relative">
              <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
              <input type="text" name="search" 
                placeholder="Nombre, email..." 
                value="{{ request('search') }}"
                class="w-full pl-11 pr-4 py-3 border-2 border-slate-200 rounded-xl focus:border-orange-500 focus:outline-none focus:ring-0 transition-colors duration-200 bg-white">
            </div>
          </div>

          <!-- Rol -->
          <div class="group">
            <label class="block text-sm font-medium text-slate-800 mb-2">Rol</label>
            <div class="relative">
              <select name="role" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl focus:border-orange-500 focus:outline-none focus:ring-0 transition-colors duration-200 bg-white appearance-none cursor-pointer">
                <option value="">Todos los roles</option>
                @foreach($roles as $role)
                  <option value="{{ $role->name }}" {{ request('role') === $role->name ? 'selected' : '' }}>{{ ucfirst($role->name) }}</option>
                @endforeach
              </select>
              <i class="fas fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"></i>
            </div>
          </div>

          <!-- Estado -->
          <div class="group">
            <label class="block text-sm font-medium text-slate-800 mb-2">Estado</label>
            <div class="relative">
              <select name="status" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl focus:border-orange-500 focus:outline-none focus:ring-0 transition-colors duration-200 bg-white appearance-none cursor-pointer">
                <option value="">Todos los estados</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Activo</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactivo</option>
                <option value="blocked" {{ request('status') === 'blocked' ? 'selected' : '' }}>Bloqueado</option>
              </select>
              <i class="fas fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"></i>
            </div>
          </div>

          <!-- Botones de acción -->
          <div class="flex items-end gap-3 md:col-span-2 md:justify-start lg:justify-end">
            <button type="submit" class="flex-1 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white font-semibold py-3 rounded-xl transition-all duration-300 shadow-md hover:shadow-lg flex items-center justify-center gap-2 group">
              <i class="fas fa-search group-hover:scale-110 transition-transform duration-300"></i>
              <span>Filtrar</span>
            </button>
            <a href="{{ route('admin.usuarios.index') }}" class="px-4 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl transition-all duration-300 flex items-center justify-center" title="Limpiar filtros">
              <i class="fas fa-redo text-lg"></i>
            </a>
          </div>
        </div>
      </form>
    </div>

    <!-- ========== TABLA MODERNA Y ELEGANTE ========== -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-slate-100 mb-8">
      <div class="overflow-x-auto">
        <table class="w-full">
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
                    'administrador' => '<span class="inline-block px-4 py-2 bg-red-100 text-red-700 text-xs font-bold rounded-full border border-red-300"><i class="fas fa-user-shield mr-2"></i>' . ucfirst($usuario->display_role) . '</span>',
                    'recepcion' => '<span class="inline-block px-4 py-2 bg-blue-100 text-blue-700 text-xs font-bold rounded-full border border-blue-300"><i class="fas fa-door-open mr-2"></i>' . ucfirst($usuario->display_role) . '</span>',
                    'reservas' => '<span class="inline-block px-4 py-2 bg-amber-100 text-amber-700 text-xs font-bold rounded-full border border-amber-300"><i class="fas fa-calendar mr-2"></i>' . ucfirst($usuario->display_role) . '</span>',
                    'mantenimiento' => '<span class="inline-block px-4 py-2 bg-purple-100 text-purple-700 text-xs font-bold rounded-full border border-purple-300"><i class="fas fa-wrench mr-2"></i>' . ucfirst($usuario->display_role) . '</span>',
                    'minibar' => '<span class="inline-block px-4 py-2 bg-green-100 text-green-700 text-xs font-bold rounded-full border border-green-300"><i class="fas fa-bottle-water mr-2"></i>' . ucfirst($usuario->display_role) . '</span>',
                    default => '<span class="inline-block px-4 py-2 bg-slate-100 text-slate-700 text-xs font-bold rounded-full border border-slate-300"><i class="fas fa-user mr-2"></i>' . ucfirst($usuario->display_role) . '</span>',
                  } : '<span class="text-slate-400 text-sm">Sin rol</span>' !!}
                </td>
                <td class="px-4 py-3">
                  @if($usuario->status === 'active')
                    <span class="inline-flex items-center gap-2 px-3 py-1.5 bg-green-100 text-green-700 text-xs font-bold rounded-full border border-green-300">
                      <span class="inline-block w-2 h-2 bg-green-600 rounded-full animate-pulse"></span>
                      Activo
                    </span>
                  @elseif($usuario->status === 'blocked')
                    <span class="inline-flex items-center gap-2 px-3 py-1.5 bg-red-100 text-red-700 text-xs font-bold rounded-full border border-red-300">
                      <span class="inline-block w-2 h-2 bg-red-600 rounded-full animate-pulse"></span>
                      Bloqueado
                    </span>
                  @else
                    <span class="inline-flex items-center gap-2 px-3 py-1.5 bg-gray-100 text-gray-700 text-xs font-bold rounded-full border border-gray-300">
                      <span class="inline-block w-2 h-2 bg-gray-600 rounded-full animate-pulse"></span>
                      Inactivo
                    </span>
                  @endif
                </td>
                <td class="px-4 py-3 text-sm text-slate-600">
                  <div class="flex items-center gap-2">
                    <i class="fas fa-clock text-slate-400 mr-2"></i>
                    <span>{{ $usuario->last_login_at ? $usuario->last_login_formatted : 'Nunca' }}</span>
                  </div>
                </td>
                <td class="px-4 py-3">
                  <div class="flex items-center justify-center gap-3 opacity-0 group-hover:opacity-100 transition-all duration-300">
                    <a href="{{ route('admin.usuarios.edit', $usuario) }}" 
                      class="p-2.5 bg-orange-100 hover:bg-orange-500 text-orange-600 hover:text-white rounded-lg transition-all duration-300 shadow-md hover:shadow-lg hover:scale-110 transform"
                      title="Editar usuario">
                      <i class="fas fa-edit"></i>
                    </a>
                    <a href="{{ route('admin.usuarios.activity', $usuario) }}" 
                      class="p-2.5 bg-blue-100 hover:bg-blue-500 text-blue-600 hover:text-white rounded-lg transition-all duration-300 shadow-md hover:shadow-lg hover:scale-110 transform"
                      title="Ver actividad">
                      <i class="fas fa-history"></i>
                    </a>
                    <a href="{{ route('admin.usuarios.sessions', $usuario) }}" 
                      class="p-2.5 bg-amber-100 hover:bg-amber-500 text-amber-600 hover:text-white rounded-lg transition-all duration-300 shadow-md hover:shadow-lg hover:scale-110 transform"
                      title="Gestionar sesiones">
                      <i class="fas fa-wifi"></i>
                    </a>
                    <button type="button" 
                      class="delete-btn p-2.5 bg-red-100 hover:bg-red-500 text-red-600 hover:text-white rounded-lg transition-all duration-300 shadow-md hover:shadow-lg hover:scale-110 transform"
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
<form id="deleteForm" action="" method="POST" style="display: none;">
  @csrf
  @method('DELETE')
</form>

<!-- Modal de confirmación elegante -->
<div id="deleteModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center backdrop-blur-sm" style="display: none;">
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

<script>
  // Sistema de eliminación mejorado
  let deleteUserId = null;

  document.querySelectorAll('.delete-btn').forEach(btn => {
    btn.addEventListener('click', function(e) {
      e.preventDefault();
      deleteUserId = this.dataset.id;
      document.getElementById('deleteModal').style.display = 'flex';
    });
  });

  document.getElementById('cancelDelete').addEventListener('click', function() {
    document.getElementById('deleteModal').style.display = 'none';
    deleteUserId = null;
  });

  document.getElementById('confirmDelete').addEventListener('click', function() {
    if (deleteUserId) {
      document.getElementById('deleteForm').action = `/admin/usuarios/${deleteUserId}`;
      document.getElementById('deleteForm').submit();
    }
  });

  // Cerrar modal al presionar Esc
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
      document.getElementById('deleteModal').style.display = 'none';
      deleteUserId = null;
    }
  });
</script>

<style>
  /* Animaciones suaves */
  @keyframes slideIn {
    from {
      transform: translateY(-20px);
      opacity: 0;
    }
    to {
      transform: translateY(0);
      opacity: 1;
    }
  }

  .bg-gradient-to-br {
    background-image: linear-gradient(to bottom right, var(--tw-gradient-stops));
  }

  .from-orange-500 { --tw-gradient-from: #f97316; }
  .to-orange-600 { --tw-gradient-to: #ea580c; }
  .from-slate-50 { --tw-gradient-from: #f8fafc; }
  .to-slate-100 { --tw-gradient-to: #f1f5f9; }

  /* Mejoras de accesibilidad */
  input:focus-visible,
  select:focus-visible {
    outline: none;
  }

  /* Animación de pulso */
  @keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: .5; }
  }

  .animate-pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
  }

  /* Selects personalizados */
  select {
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23cbd5e1' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 1rem center;
    padding-right: 2.5rem;
  }

  /* Hover effects mejorados */
  .group:hover .group-hover\:opacity-100 {
    opacity: 1;
  }

  .transition-all {
    transition-property: all;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 300ms;
  }

  /* Modal backdrop */
  .backdrop-blur-sm {
    backdrop-filter: blur(4px);
  }

  /* Responsive adjustments */
  @media (max-width: 768px) {
    .overflow-x-auto {
      font-size: 0.875rem;
    }
  }
</style>
@endsection
