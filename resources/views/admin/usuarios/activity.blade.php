@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/usuarios/usuarios.css') }}">
<link rel="stylesheet" href="{{ asset('css/usuarios/dashboard.css') }}">
<link rel="stylesheet" href="{{ asset('css/usuarios/tablas.css') }}">
<link rel="stylesheet" href="{{ asset('css/usuarios/edit.css') }}">
@endpush

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 usuario-page">
  <div class="px-4 sm:px-6 lg:px-8 py-6">
    
    {{-- NUEVO HERO PREMIUM --}}
    <div class="activity-hero-card mb-5">
      <div class="activity-hero-icon">
        <i class="fas fa-user-shield"></i>
      </div>
      <div class="activity-hero-info">
        <h1 class="activity-hero-title">Historial de Actividad</h1>
        <p class="activity-hero-description">
          Registro de seguridad y sesiones para <strong class="text-slate-800">{{ $usuario->name }} {{ $usuario->last_name }}</strong>
        </p>
      </div>
      <div class="activity-hero-actions">
        <a href="{{ route('admin.usuarios.edit', $usuario) }}" class="btn-back-refined">
          <i class="fas fa-arrow-left"></i> VOLVER
        </a>
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 mb-5">
      <div class="usuario-stat-card border-blue-500">
        <div class="stat-head">
          <div>
            <p class="stat-title">Inicios de sesión</p>
            <p class="stat-value">{{ $usuario->activities()->logins()->count() }}</p>
          </div>
          <div class="usuario-stat-icon bg-blue-100">
            <i class="fas fa-sign-in-alt text-blue-600"></i>
          </div>
        </div>
        <p class="stat-description">Número total de accesos exitosos.</p>
      </div>
      <div class="usuario-stat-card border-green-500">
        <div class="stat-head">
          <div>
            <p class="stat-title">Cambios de contraseña</p>
            <p class="stat-value">{{ $usuario->activities()->passwordChanges()->count() }}</p>
          </div>
          <div class="usuario-stat-icon bg-green-100">
            <i class="fas fa-key text-green-600"></i>
          </div>
        </div>
        <p class="stat-description">Actualizaciones de contraseña realizadas.</p>
      </div>
      <div class="usuario-stat-card border-teal-500">
        <div class="stat-head">
          <div>
            <p class="stat-title">Últimos 30 días</p>
            <p class="stat-value">{{ $usuario->activities()->recent()->count() }}</p>
          </div>
          <div class="usuario-stat-icon bg-teal-100">
            <i class="fas fa-chart-line text-teal-600"></i>
          </div>
        </div>
        <p class="stat-description">Acciones registradas en el último mes.</p>
      </div>
      <div class="usuario-stat-card border-orange-500">
        <div class="stat-head">
          <div>
            <p class="stat-title">Intentos fallidos</p>
            <p class="stat-value">{{ $usuario->activities()->byStatus('failed')->count() }}</p>
          </div>
          <div class="usuario-stat-icon bg-orange-100">
            <i class="fas fa-exclamation-triangle text-orange-600"></i>
          </div>
        </div>
        <p class="stat-description">Accesos denegados o fallidos detectados.</p>
      </div>
    </div>

    <div class="bg-white rounded-3xl shadow-lg border border-slate-100 overflow-hidden">
      <div class="px-6 py-5 border-b border-slate-200">
        <h2 class="h5 mb-0 text-slate-800 font-bold"><i class="fas fa-list text-teal-500 me-2"></i> Registro detallado</h2>
      </div>
      <div class="overflow-x-auto usuarios-table-wrapper">
        <table class="w-full usuarios-table">
          <thead>
            <tr>
              <th>Acción</th>
              <th>Tipo</th>
              <th>Descripción</th>
              <th>Estado</th>
              <th>IP Address</th>
              <th>Dispositivo</th>
              <th>Fecha y hora</th>
            </tr>
          </thead>
          <tbody>
            @forelse($activities as $activity)
              @php
                // Clasificación dinámica del tipo de actividad
                $actionName = strtolower($activity->action);
                $tipoActividad = match(true) {
                  str_contains($actionName, 'login') || str_contains($actionName, 'logout') => 'Autenticación',
                  str_contains($actionName, 'password') => 'Seguridad',
                  str_contains($actionName, 'role') || str_contains($actionName, 'status') => 'Permisos',
                  str_contains($actionName, 'update') => 'Edición',
                  str_contains($actionName, 'delete') => 'Eliminación',
                  default => 'Sistema'
                };
                
                $tipoColor = match($tipoActividad) {
                  'Autenticación' => 'bg-blue-50 text-blue-700 border-blue-200',
                  'Seguridad' => 'bg-amber-50 text-amber-700 border-amber-200',
                  'Permisos' => 'bg-purple-50 text-purple-700 border-purple-200',
                  'Edición' => 'bg-slate-50 text-slate-700 border-slate-200',
                  default => 'bg-slate-50 text-slate-600 border-slate-200'
                };
              @endphp
              <tr>
                <td>
                  <span class="usuarios-badge bg-slate-100 text-slate-700 font-medium">{{ strtoupper(str_replace('_', ' ', $activity->action)) }}</span>
                </td>
                <td>
                  <span class="usuarios-badge border {{ $tipoColor }}">{{ $tipoActividad }}</span>
                </td>
                <td class="small text-slate-600">{{ $activity->description }}</td>
                <td>
                  @if($activity->status === 'success')
                    <span class="usuarios-badge bg-emerald-100 text-emerald-700">Exitosa</span>
                  @elseif($activity->status === 'failed')
                    <span class="usuarios-badge bg-red-100 text-red-700">Fallida</span>
                  @else
                    <span class="usuarios-badge bg-amber-100 text-amber-700">Intentada</span>
                  @endif
                </td>
                <td>
                  <small class="text-slate-500 font-monospace">{{ $activity->ip_address ?? '-' }}</small>
                </td>
                <td>
                  <small class="text-slate-500">{{ $activity->device_name ?? 'Desconocido' }}</small>
                </td>
                <td>
                  <small class="text-slate-500" title="{{ $activity->created_at }}">
                    {{ $activity->created_at->diffForHumans() }}
                  </small>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7" class="text-center py-8">
                  <p class="text-slate-500 mb-0">No hay actividades registradas para este usuario.</p>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    <div class="mt-4 flex justify-center">
      {{ $activities->links() }}
    </div>

    {{-- Mobile activity cards for responsive view --}}
    <div class="usuarios-cards-mobile-container mt-6">
      @forelse($activities as $activity)
        <div class="usuarios-card-mobile">
          <div class="card-top flex items-center justify-between">
            <div>
              <p class="font-semibold text-slate-900">{{ str_replace('_', ' ', $activity->action) }}</p>
              <p class="text-slate-500 text-sm mt-1">{{ $activity->activity_type ?? 'Tipo no disponible' }}</p>
            </div>
            <span class="usuarios-badge bg-slate-100 text-slate-700">{{ ucfirst($activity->status) }}</span>
          </div>
          <div class="card-body card-body--with-separator mt-3">
            <div class="card-data">
              <div class="label"><i class="fas fa-info-circle"></i> Descripción</div>
              <div class="value text-slate-600">{{ $activity->description }}</div>
              <div class="label"><i class="fas fa-map-marker-alt"></i> IP</div>
              <div class="value">{{ $activity->ip_address ?? '-' }}</div>
              <div class="label"><i class="fas fa-mobile-alt"></i> Dispositivo</div>
              <div class="value">{{ $activity->device_name ?? 'Desconocido' }}</div>
              <div class="label"><i class="fas fa-calendar-alt"></i> Fecha</div>
              <div class="value">{{ $activity->created_at->diffForHumans() }}</div>
            </div>
          </div>
        </div>
      @empty
        <div class="usuarios-card-mobile">
          <div class="card-body">
            <p class="text-slate-500 text-center">No hay actividades registradas para este usuario.</p>
          </div>
        </div>
      @endforelse
    </div>
  </div>
</div>
@endsection
