<link rel="stylesheet" href="{{ asset('css/blade/reception/partials/folio-form--style1.css') }}">

<div id="notification-container"></div>

<div
    class="folio-section"
    id="folio-section-config"
    data-guests-url="{{ route('reception.folio.guests') }}"
    data-search-url="{{ route('reception.folio.search') }}"
>
    <div class="folio-header">
        <h2><i class="bi bi-receipt"></i> Folio y Cargos</h2>
        <p>Buscar folio por estancia o habitación, agregar cargos o registrar pagos</p>
    </div>

    <div class="folio-card">
        <h3>Buscar Folio</h3>
        <form id="folio-search" class="mt-3">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Huésped</label>
                    <select id="folio-guest" class="form-select form-select-lg">
                        <option value="">Seleccione un huésped...</option>
                    </select>
                    <div id="guest-loading" class="text-muted mt-2" style="display:none;">
                        <i class="bi bi-hourglass-split"></i> Cargando huéspedes...
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Habitación</label>
                    <input id="folio-room" class="form-control form-control-lg" placeholder="Ingrese número de habitación" />
                </div>
                <div class="col-12 mt-4">
                    <button type="button" id="folio-search-btn" class="btn-search-folio">
                        <i class="bi bi-search"></i> Buscar Folio
                    </button>
                </div>
            </div>
        </form>

        <div id="folio-sim-result" class="mt-4"></div>
    </div>

    <div id="folio-actions" style="display:none;">
        <div class="action-columns">
            <div class="action-column">
                <h3 class="folio-section-title">
                    <i class="bi bi-plus-circle"></i> Agregar Cargo
                </h3>
                <form id="add-charge-form">
                    <input type="hidden" id="current-stay-id" />
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tipo de Cargo</label>
                        <select class="form-select form-select-lg" id="charge-source">
                            <option value="Minibar">Minibar</option>
                            <option value="Restaurante">Restaurante</option>
                            <option value="Lavandería">Lavandería</option>
                            <option value="Servicio a la habitación">Servicio a la habitación</option>
                            <option value="Bebidas">Bebidas</option>
                            <option value="Otros">Otros</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Descripción</label>
                        <input class="form-control form-control-lg" id="charge-desc" placeholder="Descripción del cargo" required />
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Monto (COP)</label>
                        <input class="form-control form-control-lg" id="charge-amount" placeholder="0.00" type="number" step="0.01" required />
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">IVA (%)</label>
                        <input class="form-control form-control-lg" id="charge-tax" placeholder="Ej: 19" type="number" step="0.1" />
                    </div>
                    <button type="submit" class="btn-add-charge">
                        <i class="bi bi-plus-lg"></i> Agregar Cargo
                    </button>
                </form>
            </div>

            <div class="action-column">
                <h3 class="folio-section-title">
                    <i class="bi bi-credit-card"></i> Registrar Pago
                </h3>
                <form id="add-payment-form">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Método de pago</label>
                        <select class="form-select form-select-lg" id="pay-method" required>
                            <option value="">Seleccione...</option>
                            <option value="Efectivo">Efectivo</option>
                            <option value="Tarjeta Débito">Tarjeta Débito</option>
                            <option value="Tarjeta Crédito">Tarjeta Crédito</option>
                            <option value="Transferencia">Transferencia</option>
                            <option value="Cheque">Cheque</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Monto (COP)</label>
                        <input class="form-control form-control-lg" id="pay-amount" placeholder="0.00" type="number" step="0.01" required />
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Descripción</label>
                        <input class="form-control form-control-lg" id="pay-description" placeholder="Abono a estancia, etc..." />
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Referencia Externa (opcional)</label>
                        <input class="form-control form-control-lg" id="pay-reference" placeholder="Nº transacción, comprobante..." />
                    </div>
                    <button type="submit" class="btn-add-payment">
                        <i class="bi bi-check-lg"></i> Registrar Pago
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('js/reception-folio-form.js') }}"></script>


