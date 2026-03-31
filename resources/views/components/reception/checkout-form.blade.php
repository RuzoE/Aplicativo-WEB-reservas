<link rel="stylesheet" href="{{ asset('css/blade/reception/partials/checkout-form--style1.css') }}">

<!-- Modal de Confirmación Check-out -->
<div class="modal fade" id="confirmCheckoutModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="bi bi-exclamation-triangle"></i> Confirmar Check-out</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <p class="fs-5">¿Está seguro de procesar el Check-out para <strong id="confirm-co-guest"></strong>?</p>
                <p class="text-muted">Esto cerrará la estancia de forma permanente y liberará la habitación.</p>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger px-4" id="btn-confirm-checkout-finalize">SÍ, PROCESAR SALIDA</button>
            </div>
        </div>
    </div>
</div>

<div id="checkout-notification-container" class="checkout-notification"></div>

<div
    class="checkout-section"
    id="checkout-section-config"
    data-folio-search-url="{{ route('reception.folio.search') }}"
    data-dashboard-url="{{ route('reception.dashboard') }}"
>
    <div class="checkout-header">
        <h2><i class="bi bi-box-arrow-in-right"></i> Check-out</h2>
        <p>Procesa la salida de huéspedes y genera el comprobante final</p>
    </div>

    <!-- Buscador -->
    <div class="checkout-card mb-4">
        <h3>Buscar Estancia para Check-out</h3>
        <div class="row g-3 align-items-end">
            <div class="col-md-8">
                <label class="form-label fw-bold">Buscar por Nombre, Documento o Habitación</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" id="co-search-input" class="form-control form-control-lg" placeholder="Ej. Juan, 12345678, o 101">
                </div>
            </div>
            <div class="col-md-4">
                <button type="button" id="co-btn-search" class="btn btn-primary btn-lg w-100">
                    Buscar
                </button>
            </div>
        </div>
        <div id="co-search-error" class="text-danger mt-2" style="display:none;"></div>
    </div>

    <!-- Contenedor Detalles de Estancia y Checkout (Oculto inicialmente) -->
    <div id="co-details-container" style="display:none;">
        <div class="row g-4 mb-4">
            <!-- Detalles del Huésped -->
            <div class="col-lg-6">
                <div class="checkout-card h-100 mb-0">
                    <h4 class="mb-3 text-primary"><i class="bi bi-person-badge"></i> Detalles del Huésped</h4>
                    <table class="table table-borderless mb-0">
                        <tbody>
                            <tr>
                                <th class="ps-0" style="width: 40%; color: #666;">Nombre:</th>
                                <td id="co-guest-name" class="fw-bold text-dark"></td>
                            </tr>
                            <tr>
                                <th class="ps-0" style="color: #666;">Documento:</th>
                                <td id="co-guest-doc" class="text-dark"></td>
                            </tr>
                            <tr>
                                <th class="ps-0" style="color: #666;">Habitación:</th>
                                <td id="co-guest-room" class="fw-bold text-primary"></td>
                            </tr>
                            <tr>
                                <th class="ps-0" style="color: #666;">Check-in Date:</th>
                                <td id="co-guest-checkin" class="text-dark"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Resumen de Folio -->
            <div class="col-lg-6">
                <div class="checkout-card h-100 mb-0">
                    <h4 class="mb-3 text-primary"><i class="bi bi-receipt"></i> Resumen de Folio</h4>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span style="color: #666; font-size: 1.1rem;">Cargos Totales:</span>
                        <span id="co-total-charges" class="fw-bold" style="font-size: 1.1rem;">$0.00</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span style="color: #666; font-size: 1.1rem;">Pagos Realizados:</span>
                        <span id="co-total-payments" class="fw-bold text-success" style="font-size: 1.1rem;">$0.00</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                        <span class="fw-bold text-dark" style="font-size: 1.3rem;">Saldo Pendiente:</span>
                        <span id="co-balance" class="fw-bold text-danger" style="font-size: 1.5rem;">$0.00</span>
                    </div>
                </div>
            </div>
        </div>


        <!-- Procesar Salida -->
        <div class="checkout-card text-center">
            <h3 class="mb-4">Confirmación de Salida</h3>
            <p class="text-muted mb-4">Una vez procesado el Check-out, la habitación quedará disponible y la estancia se cerrará.</p>
            <input type="hidden" id="co-stay-id">
            <button type="button" id="co-btn-process" class="btn-process-checkout" disabled>
                <i class="bi bi-check-circle"></i> Procesar Check-out
            </button>
            <div id="co-process-msg" class="mt-3"></div>
        </div>
    </div>

    <div class="checkout-info-box">
        <h4><i class="bi bi-info-circle"></i> Proceso de Check-out</h4>
        <ul>
            <li>Busca la estancia por nombre, documento o habitación.</li>
            <li>Verifica que el saldo pendiente sea $0.00.</li>
            <li>Si hay saldo, registra el pago antes de poder hacer check-out.</li>
            <li>Al procesar el check-out, la habitación se libera y cambia a "Disponible".</li>
        </ul>
    </div>
</div>

<script src="{{ asset('js/reception-checkout-form.js') }}"></script>


