@extends('layouts.app')

@section('content')
@php
    $adminView = true;
    $sidebarView = 'admin.sidebar';
    $frequencyLabels = [
        'daily' => 'Diario',
        'weekly' => 'Semanal',
        'monthly' => 'Mensual',
    ];
@endphp

<link rel="stylesheet" href="{{ asset('css/blade/admin/backups/index--style1.css') }}">

<div class="backup-wrapper">
    <div class="backup-container">

        <section class="backup-hero">
            <div class="backup-hero-content">
                <div>
                    <span class="backup-pill">
                        <i class="bi bi-cloud-check"></i>
                        Backups + Google Drive
                    </span>
                    <h1>Gestión de Backups</h1>
                    <p>
                        Administra copias de seguridad del sistema almacenadas en la nube (Google Drive)
                        con una vista centralizada, segura y lista para uso administrativo.
                    </p>
                </div>

            <div class="backup-generate-controls text-center">
                <form method="POST" action="{{ route('admin.backups.generate') }}" id="backup-generate-form" class="backup-generate-form">
                    @csrf
                    <button type="submit" class="backup-btn-primary {{ $summary['last_status'] === 'En proceso' ? 'disabled' : '' }}" 
                            id="generate-backup-btn"
                            {{ $summary['last_status'] === 'En proceso' ? 'disabled' : '' }}>
                        <i class="bi {{ $summary['last_status'] === 'En proceso' ? 'bi-arrow-repeat' : 'bi-cloud-arrow-up' }}"></i>
                        <span>{{ $summary['last_status'] === 'En proceso' ? 'Backup en proceso...' : 'Respaldar Base de Datos' }}</span>
                    </button>
                    <div class="mt-2 text-muted">
                        <small>Respaldo <strong>Rápido</strong>: solo incluye la base de datos (SQL) y subida a Google Drive.</small>
                    </div>
                </form>

                @if($summary['last_status'] === 'En proceso')
                    <div class="mt-4 p-3 border rounded bg-white shadow-sm" style="max-width: 450px; margin: 0 auto; border-style: dashed !important; border-color: #fca5a5 !important;">
                        <form method="POST" action="{{ route('admin.backups.reset') }}">
                            @csrf
                            <div class="d-flex align-items-center gap-2 mb-2 justify-content-center text-danger">
                                <i class="bi bi-exclamation-triangle-fill"></i>
                                <span class="fw-bold">¿PROCESO BLOQUEADO?</span>
                            </div>
                            <button type="submit" class="btn btn-danger btn-sm w-100 fw-bold py-2" style="border-radius: 8px;">
                                <i class="bi bi-x-circle"></i> REINICIAR ESTADO AHORA
                            </button>
                            <p class="mb-0 mt-2 text-muted small">
                                Usa esto solo si el botón lleva más de 2-3 minutos sin responder.
                            </p>
                        </form>
                    </div>
                @endif
            </div>
            </div>
        </section>

        <section class="backup-stats-grid">
            <article class="backup-stat-card">
                <div class="backup-stat-top">
                    <span>Total de backups</span>
                    <i class="bi bi-archive"></i>
                </div>
                <strong>{{ number_format($summary['total']) }}</strong>
                <small>Archivos visibles en Google Drive</small>
            </article>

            <article class="backup-stat-card">
                <div class="backup-stat-top">
                    <span>Último backup realizado</span>
                    <i class="bi bi-clock-history"></i>
                </div>
                <strong>{{ $summary['last_backup_label'] }}</strong>
                <small>{{ $summary['last_backup_human'] }}</small>
            </article>

            <article class="backup-stat-card">
                <div class="backup-stat-top">
                    <span>Estado del último backup</span>
                    <i class="bi bi-shield-check"></i>
                </div>
                <strong>
                    <span class="backup-status backup-status-{{ $summary['last_status_color'] }}">
                        {{ $summary['last_status'] }}
                    </span>
                </strong>
                <small>{{ $summary['last_message'] }}</small>
            </article>

            <article class="backup-stat-card">
                <div class="backup-stat-top">
                    <span>Almacenamiento</span>
                    <i class="bi bi-google"></i>
                </div>
                <strong>{{ $summary['storage'] }}</strong>
                <small>Carpeta de backups del disco configurado</small>
            </article>
        </section>

        <section class="backup-grid">
            <article class="backup-card">
                <div class="backup-card-header">
                    <div>
                        <h2>Automatización</h2>
                        <p>Programa la ejecución automática de los respaldos del sistema.</p>
                    </div>
                    <span class="backup-badge">Actual: {{ $frequencyLabels[$settings->frequency] ?? 'Diario' }}</span>
                </div>

                <form method="POST" action="{{ route('admin.backups.schedule') }}" class="backup-schedule-form">
                    @csrf
                    @method('PUT')

                    <label for="frequency" class="backup-label">Frecuencia automática</label>
                    <div class="backup-inline-form">
                        <select name="frequency" id="frequency" class="backup-select">
                            @foreach($frequencyLabels as $value => $label)
                                <option value="{{ $value }}" @selected($settings->frequency === $value)>{{ $label }}</option>
                            @endforeach
                        </select>

                        <button type="submit" class="backup-btn-secondary">
                            <i class="bi bi-calendar-check"></i>
                            Guardar programación
                        </button>
                    </div>

                    <p class="backup-help-text">
                        El scheduler de Laravel ejecutará el backup completo a las 02:00 AM con frecuencia
                        <strong>{{ strtolower($frequencyLabels[$settings->frequency] ?? 'diaria') }}</strong>.
                    </p>
                </form>
            </article>

            <article class="backup-card">
                <div class="backup-card-header">
                    <div>
                        <h2>Integración activa</h2>
                        <p>El módulo usa <code>Storage::disk('google')</code> y la configuración existente del proyecto.</p>
                    </div>
                </div>

                <ul class="backup-checklist">
                    <li><i class="bi bi-check-circle-fill"></i> Solo accesible para administradores.</li>
                    <li><i class="bi bi-check-circle-fill"></i> Descarga y eliminación con confirmación.</li>
                    <li><i class="bi bi-check-circle-fill"></i> Respaldo completo de BD + archivos del sistema.</li>
                    <li><i class="bi bi-check-circle-fill"></i> Subida automática a Google Drive.</li>
                </ul>
            </article>
        </section>

        <section class="backup-card backup-table-card">
            <div class="backup-card-header">
                <div>
                    <h2>Listado de backups</h2>
                    <p>Se muestran únicamente los archivos ZIP disponibles en la carpeta configurada para backups.</p>
                </div>
                <span class="backup-badge">{{ number_format($backups->count()) }} archivo(s)</span>
            </div>

            <div class="backup-table-wrap">
                <table class="backup-table">
                    <thead>
                        <tr>
                            <th>Nombre del archivo</th>
                            <th>Fecha</th>
                            <th>Tamaño</th>
                            <th>Ubicación</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($backups as $backup)
                            <tr>
                                <td>
                                    <div class="backup-file-name">
                                        <i class="bi bi-file-earmark-zip"></i>
                                        <span>{{ $backup['name'] }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div>{{ $backup['formatted_date'] }}</div>
                                    <small>{{ $backup['human_diff'] }}</small>
                                </td>
                                <td>{{ $backup['size_human'] }}</td>
                                <td>{{ $backup['location'] }}</td>
                                <td>
                                    <span class="backup-status backup-status-{{ $backup['status_color'] }}">
                                        {{ $backup['status'] }}
                                    </span>
                                </td>
                                <td>
                                    <div class="backup-actions">
                                        <a href="{{ route('admin.backups.download', ['path' => base64_encode($backup['path'])]) }}" class="backup-link-action">
                                            <i class="bi bi-download"></i>
                                            Descargar
                                        </a>

                                        <form method="POST" action="{{ route('admin.backups.destroy') }}" class="delete-backup-form" data-backup-name="{{ $backup['name'] }}">
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="path" value="{{ base64_encode($backup['path']) }}">
                                            <button type="submit" class="backup-link-action backup-link-danger">
                                                <i class="bi bi-trash"></i>
                                                Eliminar
                                            </button>
                                        </form>

                                        <button type="button" 
                                                class="backup-link-action restore-backup-btn" 
                                                style="background: #fff7ed; color: #9a3412;"
                                                data-path="{{ base64_encode($backup['path']) }}"
                                                data-name="{{ $backup['name'] }}"
                                                data-date="{{ $backup['formatted_date'] }}"
                                                data-size="{{ $backup['size_human'] }}">
                                            <i class="bi bi-arrow-counterclockwise"></i>
                                            Restaurar
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">
                                    <div class="backup-empty-state">
                                        <i class="bi bi-inbox"></i>
                                        <h3>No hay backups disponibles</h3>
                                        <p>Genera el primer respaldo o revisa la conexión del disco <code>google</code>.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</div>

{{-- Modal de Restauración --}}
<div class="modal fade" id="restoreModal" tabindex="-1" aria-labelledby="restoreModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 20px; border: none; overflow: hidden; box-shadow: 0 20px 50px rgba(0,0,0,0.2);">
            <div class="modal-header bg-danger text-white border-0 p-4">
                <h5 class="modal-title fw-bold" id="restoreModalLabel">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> Confirmar Restauración
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="alert alert-warning border-0 shadow-sm mb-4" style="background-color: #fffbeb; color: #92400e; border-radius: 12px;">
                    <div class="d-flex gap-3">
                        <i class="bi bi-info-circle-fill fs-4"></i>
                        <div>
                            <strong>Advertencia Crítica:</strong><br>
                            Esta acción reemplazará completamente la base de datos actual. Antes de proceder, el sistema generará un backup de seguridad automático del estado actual.
                        </div>
                    </div>
                </div>

                <div class="backup-details mb-4 p-3 border rounded-3 bg-light">
                    <p class="mb-1 text-muted small text-uppercase fw-bold">Backup seleccionado:</p>
                    <div class="d-flex align-items-center gap-3">
                        <i class="bi bi-file-earmark-zip-fill fs-2 text-primary"></i>
                        <div>
                            <div class="fw-bold text-dark" id="modal-backup-name">---</div>
                            <div class="small text-muted">
                                <span id="modal-backup-date">---</span> &bull; <span id="modal-backup-size">---</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="confirmation-check">
                    <label for="restore-confirmation" class="form-label fw-bold small text-muted">Para continuar, escribe <span class="text-danger">CONFIRMAR</span> abajo:</label>
                    <input type="text" id="restore-confirmation" class="form-control form-control-lg text-center" placeholder="Escribe aquí..." autocomplete="off" style="border-radius: 12px; border: 2px solid #e2e8f0; font-weight: bold; letter-spacing: 2px;">
                </div>
            </div>
            <div class="modal-footer border-0 p-4 pt-0">
                <button type="button" class="btn btn-light px-4 py-2 fw-600" data-bs-dismiss="modal" style="border-radius: 10px;">Cancelar</button>
                <button type="button" id="confirm-restore-btn" class="btn btn-danger px-4 py-2 fw-600 disabled" style="border-radius: 10px;">
                    <i class="bi bi-arrow-counterclockwise"></i> Ejecutar Restauración
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Overlay de Carga --}}
<div id="restore-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.9); z-index: 99999; flex-direction: column; align-items: center; justify-content: center; color: #fff;">
    <div class="spinner-border text-primary mb-3" role="status" style="width: 4rem; height: 4rem; border-width: 0.4em;"></div>
    <h3 class="fw-bold mb-2">Restaurando Sistema...</h3>
    <p class="text-muted text-center" style="max-width: 400px; padding: 0 20px;">
        Por favor, no cierres esta ventana. Estamos realizando una <strong>copia de seguridad previa</strong> por seguridad, descargando y restaurando la base de datos.
    </p>
</div>

@push('scripts')
<script src="{{ asset('js/blade/admin/backups/index--script1.js') }}"></script>
@endpush
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        @if(session('success') || session('message') || session('status'))
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: '{{ session('success') ?? session('message') ?? session('status') }}',
                timer: 3500,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        @endif

        @if($errors->any())
            Swal.fire({
                icon: 'error',
                title: 'Atención',
                text: '{{ $errors->first() }}',
                timer: 4500,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '{{ session('error') }}',
                timer: 5000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        @endif
    });
</script>
@endpush
@endsection
