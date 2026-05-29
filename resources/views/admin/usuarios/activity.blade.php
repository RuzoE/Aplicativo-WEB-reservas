@extends('layouts.app')

@section('content')
<div class="container-fluid">
  <div class="row mb-4">
    <div class="col">
      <h2 class="mb-1"><i class="bi bi-clock-history me-2"></i>Actividad de usuario</h2>
      <p class="text-muted mb-0">{{ $usuario->name }} {{ $usuario->last_name }} ({{ $usuario->email }})</p>
    </div>
    <div class="col-auto">
      <a href="{{ route('admin.usuarios.edit', $usuario) }}" class="btn btn-outline-secondary btn-lg">
        <i class="bi bi-arrow-left"></i> Volver
      </a>
    </div>
  </div>

  <!-- Estadísticas rápidas -->
  <div class="row mb-4">
    <div class="col-md-3">
      <div class="card shadow-sm border-0 text-center py-4">
        <div class="h3 mb-0 text-primary">{{ $usuario->activities()->logins()->count() }}</div>
        <small class="text-muted">Inicios de sesión</small>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card shadow-sm border-0 text-center py-4">
        <div class="h3 mb-0 text-success">{{ $usuario->activities()->passwordChanges()->count() }}</div>
        <small class="text-muted">Cambios de contraseña</small>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card shadow-sm border-0 text-center py-4">
        <div class="h3 mb-0 text-info">{{ $usuario->activities()->recent()->count() }}</div>
        <small class="text-muted">Actividades últimos 30 días</small>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card shadow-sm border-0 text-center py-4">
        <div class="h3 mb-0 text-warning">{{ $usuario->activities()->byStatus('failed')->count() }}</div>
        <small class="text-muted">Intentos fallidos</small>
      </div>
    </div>
  </div>

  <!-- Tabla de actividades -->
  <div class="card shadow-sm border-0">
    <div class="card-header bg-light">
      <h6 class="mb-0">Historial de actividad</h6>
    </div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
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
              <tr>
                <td>
                  <span class="badge bg-secondary text-capitalize">{{ str_replace('_', ' ', $activity->action) }}</span>
                </td>
                <td>
                  @if($activity->activity_type)
                    <span class="badge bg-light text-dark">{{ $activity->activity_type }}</span>
                  @else
                    <span class="text-muted small">-</span>
                  @endif
                </td>
                <td class="small">{{ $activity->description }}</td>
                <td>
                  @if($activity->status === 'success')
                    <span class="badge bg-success">Exitosa</span>
                  @elseif($activity->status === 'failed')
                    <span class="badge bg-danger">Fallida</span>
                  @else
                    <span class="badge bg-warning">Intentada</span>
                  @endif
                </td>
                <td>
                  <small class="text-muted font-monospace">{{ $activity->ip_address ?? '-' }}</small>
                </td>
                <td>
                  <small class="text-muted">{{ $activity->device_name ?? 'Desconocido' }}</small>
                </td>
                <td>
                  <small class="text-muted" title="{{ $activity->created_at }}">
                    {{ $activity->created_at->diffForHumans() }}
                  </small>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7" class="text-center py-5">
                  <p class="text-muted mb-0">No hay actividades registradas</p>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Paginación -->
  <div class="d-flex justify-content-center mt-4">
    {{ $activities->links() }}
  </div>
</div>
@endsection
