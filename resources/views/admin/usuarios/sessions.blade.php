@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/usuarios/usuarios.css') }}">
<link rel="stylesheet" href="{{ asset('css/usuarios/dashboard.css') }}">
<link rel="stylesheet" href="{{ asset('css/usuarios/tablas.css') }}">
<link rel="stylesheet" href="{{ asset('css/usuarios/edit.css') }}">
@endpush

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 usuario-page"
     id="sessions-wrapper"
     data-user-id="{{ $usuario->id }}">
  <div class="px-4 sm:px-6 lg:px-8 py-6">

    {{-- NUEVO HERO PREMIUM --}}
    <div class="activity-hero-card mb-5">
      <div class="activity-hero-icon" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); box-shadow: 0 8px 20px rgba(217,119,6,0.3);">
        <i class="fas fa-wifi"></i>
      </div>
      <div class="activity-hero-info">
        <h1 class="activity-hero-title">Sesiones activas</h1>
        <p class="activity-hero-description">
          Dispositivos conectados actualmente de <strong class="text-slate-800">{{ $usuario->name }} {{ $usuario->last_name }}</strong>
        </p>
      </div>
      <div class="activity-hero-actions">
        <a href="{{ route('admin.usuarios.edit', $usuario) }}" class="btn-back-refined">
          <i class="fas fa-arrow-left"></i> VOLVER
        </a>
      </div>
    </div>

  <!-- Sesiones activas -->
  <div class="bg-white rounded-3xl shadow-lg border border-slate-100 overflow-hidden mb-5">
    <div class="px-6 py-5 border-b border-slate-200 d-flex justify-content-between align-items-center">
      <h2 class="h5 mb-0 text-slate-800 font-bold"><i class="fas fa-check-circle text-emerald-500 me-2"></i> Sesiones activas</h2>
      <span class="usuarios-badge bg-emerald-100 text-emerald-700">{{ $activeSessions->count() }}</span>
    </div>
    <div class="overflow-x-auto usuarios-table-wrapper">
      @if($activeSessions->count() > 0)
        <table class="w-full usuarios-table">
          <thead>
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
                <td>
                  <span class="usuarios-badge bg-slate-100 text-slate-700 font-medium">
                    @if(str_contains($session->user_agent, 'Windows'))
                      <i class="fab fa-windows text-blue-500 me-1"></i> Windows
                    @elseif(str_contains($session->user_agent, 'Mac'))
                      <i class="fab fa-apple text-slate-600 me-1"></i> macOS
                    @elseif(str_contains($session->user_agent, 'Linux'))
                      <i class="fab fa-linux text-orange-500 me-1"></i> Linux
                    @elseif(str_contains($session->user_agent, 'iPhone'))
                      <i class="fas fa-mobile-alt text-slate-800 me-1"></i> iPhone
                    @elseif(str_contains($session->user_agent, 'Android'))
                      <i class="fab fa-android text-emerald-500 me-1"></i> Android
                    @else
                      <i class="fas fa-hdd text-slate-400 me-1"></i> Desconocido
                    @endif
                    {{ $session->device_name ? '- ' . $session->device_name : '' }}
                  </span>
                </td>
                <td class="small text-slate-600">{{ Str::limit($session->user_agent, 50) }}</td>
                <td>
                  <small class="text-slate-500 font-monospace">{{ $session->ip_address }}</small>
                </td>
                <td>
                  <small class="text-slate-500" title="{{ $session->last_activity_at }}">
                    {{ $session->last_activity_at->diffForHumans() }}
                  </small>
                </td>
                <td class="text-center">
                  <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-bold" onclick="logoutSession({{ $session->id }})">
                    <i class="fas fa-power-off me-1"></i> Cerrar
                  </button>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      @else
        <div class="py-8 text-center">
          <div class="mb-3">
            <i class="fas fa-plug text-slate-300" style="font-size: 2.5rem;"></i>
          </div>
          <p class="text-slate-500 mb-0 font-medium">No hay sesiones activas</p>
        </div>
      @endif
    </div>
  </div>

  <!-- Sesiones inactivas -->
  @if($inactiveSessions->count() > 0)
  <div class="bg-white rounded-3xl shadow-lg border border-slate-100 overflow-hidden">
    <div class="px-6 py-5 border-b border-slate-200 d-flex justify-content-between align-items-center bg-slate-50">
      <h2 class="h5 mb-0 text-slate-600 font-bold"><i class="fas fa-clock text-amber-500 me-2"></i> Sesiones inactivas (más de 24 horas)</h2>
      <span class="usuarios-badge bg-amber-100 text-amber-700">{{ $inactiveSessions->count() }}</span>
    </div>
    <div class="overflow-x-auto usuarios-table-wrapper">
      <table class="w-full usuarios-table">
        <thead>
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
            <tr class="opacity-75 bg-slate-50">
              <td>
                <span class="usuarios-badge bg-white border border-slate-200 text-slate-500 font-medium">
                  @if(str_contains($session->user_agent, 'Windows'))
                    <i class="fab fa-windows me-1"></i> Windows
                  @elseif(str_contains($session->user_agent, 'Mac'))
                    <i class="fab fa-apple me-1"></i> macOS
                  @elseif(str_contains($session->user_agent, 'Linux'))
                    <i class="fab fa-linux me-1"></i> Linux
                  @else
                    <i class="fas fa-hdd me-1"></i> Desconocido
                  @endif
                  {{ $session->device_name ? '- ' . $session->device_name : '' }}
                </span>
              </td>
              <td class="small text-slate-500">{{ Str::limit($session->user_agent, 50) }}</td>
              <td>
                <small class="text-slate-400 font-monospace">{{ $session->ip_address }}</small>
              </td>
              <td>
                <small class="text-slate-400" title="{{ $session->last_activity_at }}">
                  {{ $session->last_activity_at->diffForHumans() }}
                </small>
              </td>
              <td class="text-center">
                <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-bold" onclick="logoutSession({{ $session->id }})">
                  <i class="fas fa-power-off me-1"></i> Cerrar
                </button>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
  @endif

  </div>
</div>

@push('scripts')
<script src="{{ asset('js/usuarios/sessions.js') }}"></script>
@endpush
@endsection
