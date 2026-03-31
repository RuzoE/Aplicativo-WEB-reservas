<link rel="stylesheet" href="{{ asset('css/blade/reception/partials/user-link-form--style1.css') }}">

<!-- Modal de Confirmación Custom -->
<div class="modal fade" id="confirmLinkModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="bi bi-question-circle"></i> Confirmar Vinculación</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>¿Estás seguro de que deseas vincular a <strong id="confirm-user-name"></strong> con esta estancia?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btn-confirm-link-finalize">Sí, Vincular</button>
            </div>
        </div>
    </div>
</div>

<div id="user-link-notification-container" class="user-link-notification"></div>

<div
    class="user-link-section"
    id="user-link-section-config"
    data-guests-url="{{ route('reception.folio.guests') }}"
    data-search-url="{{ route('reception.folio.search') }}"
    data-user-search-url="{{ route('reception.users.search') }}"
    data-link-url="{{ route('reception.stay.link_user', ':id') }}"
    data-csrf-token="{{ csrf_token() }}"
>
    <div class="user-link-header">
        <h2><i class="bi bi-person-plus-fill"></i> Asociar Cuentas</h2>
        <p class="text-muted">Busca una estancia activa para vincularla con una cuenta de usuario web.</p>
    </div>

    <div class="user-link-card">
        <h3>1. Buscar Estancia Activa</h3>
        <div class="row g-3 mt-2">
            <div class="col-md-12">
                <label class="form-label fw-bold">Huésped</label>
                <select id="link-stay-guest" class="form-select form-select-lg">
                    <option value="">Seleccione un huésped...</option>
                </select>
                <div id="link-guest-loading" class="text-muted mt-2" style="display:none;">
                    <i class="bi bi-hourglass-split"></i> Cargando...
                </div>
            </div>
            <div class="col-12 mt-4">
                <button type="button" id="btn-find-stay-link" class="btn-user-link">
                    <i class="bi bi-search"></i> Buscar Estancia
                </button>
            </div>
        </div>

        <div id="link-stay-result" class="mt-4"></div>
    </div>

    <div id="link-user-actions" class="user-link-card" style="display:none;">
        <h3>2. Vincular con Cuenta Web</h3>
        <p class="text-muted">Busca el correo o nombre del usuario registrado en la página.</p>
        <div class="input-group mt-3">
            <input type="text" id="link-user-search-input" class="form-control form-control-lg" placeholder="Buscar usuario web...">
            <button class="btn btn-primary" type="button" id="btn-search-web-user">
                <i class="bi bi-search"></i>
            </button>
        </div>
        <div id="link-user-search-results" class="list-group shadow-sm mt-1" style="display:none; max-height: 250px; overflow-y: auto;">
            <!-- Results here -->
        </div>
    </div>
</div>




