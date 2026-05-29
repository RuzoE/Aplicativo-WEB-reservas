@extends('layouts.app')

@section('content')
<div class="container-fluid">
  <div class="row mb-4">
    <div class="col">
      <h2 class="mb-1"><i class="bi bi-wifi me-2"></i>Sesiones activas</h2>
      <p class="text-muted mb-0">{{ $usuario->name }} {{ $usuario->last_name }} ({{ $usuario->email }})</p>
    </div>
    <div class="col-auto">
      <a href="{{ route('admin.usuarios.edit', $usuario) }}" class="btn btn-outline-secondary btn-lg">
        <i class="bi bi-arrow-left"></i> Volver
      </a>
    </div>
  </div>

  <!-- Sesiones activas -->
  <div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-light d-flex justify-content-between align-items-center">
      <h6 class="mb-0"><i class="bi bi-check-circle text-success me-2"></i>Sesiones activas</h6>
      <span class="badge bg-success">{{ $activeSessions->count() }}</span>
    </div>
    <div class="card-body p-0">
      @if($activeSessions->count() > 0)
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th>Dispositivo</th>
                <th>Navegador/App</th>
                <th>IP Address</th>
                <th>Último acceso</th>
                <th class="text-center" style="width: 100px;">Acciones</th>
              </tr>
            </thead>
            <tbody>
              @foreach($activeSessions as $session)
                <tr>
                  <td class="fw-semibold">
                    @if(str_contains($session->user_agent, 'Windows'))
                      <i class="bi bi-windows text-primary me-2"></i>Windows
                    @elseif(str_contains($session->user_agent, 'Mac'))
                      <i class="bi bi-apple text-secondary me-2"></i>macOS
                    @elseif(str_contains($session->user_agent, 'Linux'))
                      <i class="bi bi-ubuntu text-info me-2"></i>Linux
                    @elseif(str_contains($session->user_agent, 'iPhone'))
                      <i class="bi bi-phone text-success me-2"></i>iPhone
                    @elseif(str_contains($session->user_agent, 'Android'))
                      <i class="bi bi-phone-landscape text-success me-2"></i>Android
                    @else
                      <i class="bi bi-device-ssd text-muted me-2"></i>Desconocido
                    @endif
                    {{ $session->device_name }}
                  </td>
                  <td>
                    <small class="text-muted">{{ Str::limit($session->user_agent, 50) }}</small>
                  </td>
                  <td>
                    <small class="text-muted font-monospace">{{ $session->ip_address }}</small>
                  </td>
                  <td>
                    <small class="text-muted" title="{{ $session->last_activity_at }}">
                      {{ $session->last_activity_at->diffForHumans() }}
                    </small>
                  </td>
                  <td class="text-center">
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="logoutSession({{ $session->id }})">
                      <i class="bi bi-box-arrow-right"></i> Cerrar
                    </button>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      @else
        <div class="p-5 text-center">
          <p class="text-muted mb-0">No hay sesiones activas</p>
        </div>
      @endif
    </div>
  </div>

  <!-- Sesiones inactivas -->
  @if($inactiveSessions->count() > 0)
  <div class="card shadow-sm border-0">
    <div class="card-header bg-light d-flex justify-content-between align-items-center">
      <h6 class="mb-0"><i class="bi bi-clock text-warning me-2"></i>Sesiones inactivas (más de 24 horas)</h6>
      <span class="badge bg-warning text-dark">{{ $inactiveSessions->count() }}</span>
    </div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>Dispositivo</th>
              <th>Navegador/App</th>
              <th>IP Address</th>
              <th>Último acceso</th>
              <th class="text-center" style="width: 100px;">Acciones</th>
            </tr>
          </thead>
          <tbody>
            @foreach($inactiveSessions as $session)
              <tr class="table-light">
                <td class="fw-semibold">
                  @if(str_contains($session->user_agent, 'Windows'))
                    <i class="bi bi-windows text-primary me-2"></i>Windows
                  @elseif(str_contains($session->user_agent, 'Mac'))
                    <i class="bi bi-apple text-secondary me-2"></i>macOS
                  @elseif(str_contains($session->user_agent, 'Linux'))
                    <i class="bi bi-ubuntu text-info me-2"></i>Linux
                  @else
                    <i class="bi bi-device-ssd text-muted me-2"></i>Desconocido
                  @endif
                  {{ $session->device_name }}
                </td>
                <td>
                  <small class="text-muted">{{ Str::limit($session->user_agent, 50) }}</small>
                </td>
                <td>
                  <small class="text-muted font-monospace">{{ $session->ip_address }}</small>
                </td>
                <td>
                  <small class="text-muted" title="{{ $session->last_activity_at }}">
                    {{ $session->last_activity_at->diffForHumans() }}
                  </small>
                </td>
                <td class="text-center">
                  <button type="button" class="btn btn-sm btn-outline-danger" onclick="logoutSession({{ $session->id }})">
                    <i class="bi bi-box-arrow-right"></i> Cerrar
                  </button>
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

<script>
function logoutSession(sessionId) {
  if (confirm('¿Cerrar esta sesión?')) {
    fetch(`/admin/usuarios/{{ $usuario->id }}/sesiones/${sessionId}`, {
      method: 'DELETE',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
      }
    })
    .then(r => r.json())
    .then(data => {
      if (data.success) {
        location.reload();
      }
    })
    .catch(e => alert('Error: ' + e));
  }
}
</script>
@endsection
