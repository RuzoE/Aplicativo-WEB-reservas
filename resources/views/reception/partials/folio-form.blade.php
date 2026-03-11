<style>
    .folio-section {
        padding: 40px 60px;
        max-width: 100%;
    }

    .folio-header {
        margin-bottom: 30px;
        border-bottom: 3px solid #2196F3;
        padding-bottom: 20px;
    }

    .folio-header h2 {
        color: #2196F3;
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 10px;
    }

    .folio-header p {
        color: #666;
        font-size: 1.1rem;
        margin: 0;
    }

    .folio-card {
        background: white;
        border-radius: 12px;
        padding: 30px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        margin-bottom: 30px;
    }

    .folio-card h3 {
        color: #333;
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 15px;
    }

    .folio-section-title {
        color: #333;
        font-size: 1.3rem;
        font-weight: 700;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid #e0e0e0;
    }

    .btn-search-folio {
        background: linear-gradient(135deg, #2196F3 0%, #1565C0 100%);
        color: white;
        border: none;
        padding: 12px 30px;
        border-radius: 8px;
        font-weight: 600;
        transition: transform 0.3s, box-shadow 0.3s;
        box-shadow: 0 4px 12px rgba(33, 150, 243, 0.3);
    }

    .btn-search-folio:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(33, 150, 243, 0.4);
        color: white;
    }

    .btn-add-charge {
        background: linear-gradient(135deg, #FF9800 0%, #F57C00 100%);
        color: white;
        border: none;
        padding: 12px 30px;
        border-radius: 8px;
        font-weight: 600;
        transition: transform 0.3s, box-shadow 0.3s;
        box-shadow: 0 4px 12px rgba(255, 152, 0, 0.3);
        width: 100%;
    }

    .btn-add-charge:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(255, 152, 0, 0.4);
        color: white;
    }

    .btn-add-payment {
        background: linear-gradient(135deg, #4CAF50 0%, #388E3C 100%);
        color: white;
        border: none;
        padding: 12px 30px;
        border-radius: 8px;
        font-weight: 600;
        transition: transform 0.3s, box-shadow 0.3s;
        box-shadow: 0 4px 12px rgba(76, 175, 80, 0.3);
        width: 100%;
    }

    .btn-add-payment:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(76, 175, 80, 0.4);
        color: white;
    }

    .action-columns {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
        gap: 30px;
        margin-top: 30px;
    }

    .action-column {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 25px;
        border: 2px solid #e0e0e0;
    }

    #guest-loading {
        font-size: 0.9rem;
        color: #666;
    }

    #guest-loading i {
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    .form-select-lg {
        border: 2px solid #e0e0e0;
        transition: border-color 0.3s, box-shadow 0.3s;
    }

    .form-select-lg:focus {
        border-color: #2196F3;
        box-shadow: 0 0 0 0.2rem rgba(33, 150, 243, 0.25);
    }

    .folio-notification {
        position: fixed;
        top: 20px;
        right: 20px;
        min-width: 320px;
        max-width: 500px;
        background: white;
        border-radius: 12px;
        padding: 20px 25px;
        box-shadow: 0 8px 24px rgba(0,0,0,0.15);
        z-index: 9999;
        animation: slideInRight 0.4s ease-out;
        border-left: 5px solid #2196F3;
    }

    .folio-notification.success {
        border-left-color: #4CAF50;
    }

    .folio-notification.error {
        border-left-color: #f44336;
    }

    .folio-notification.warning {
        border-left-color: #ff9800;
    }

    .folio-notification .notification-content {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .folio-notification .notification-icon {
        font-size: 28px;
        line-height: 1;
    }

    .folio-notification.success .notification-icon {
        color: #4CAF50;
    }

    .folio-notification.error .notification-icon {
        color: #f44336;
    }

    .folio-notification.warning .notification-icon {
        color: #ff9800;
    }

    .folio-notification .notification-text {
        flex: 1;
        color: #333;
        font-size: 15px;
        font-weight: 500;
    }

    .folio-notification .notification-close {
        background: none;
        border: none;
        font-size: 24px;
        color: #999;
        cursor: pointer;
        padding: 0;
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: color 0.2s;
    }

    .folio-notification .notification-close:hover {
        color: #333;
    }

    @keyframes slideInRight {
        from {
            transform: translateX(400px);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    @keyframes slideOutRight {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(400px);
            opacity: 0;
        }
    }
</style>

<div id="notification-container"></div>

<div class="folio-section">
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
                        <label class="form-label fw-bold">Impuesto (opcional)</label>
                        <input class="form-control form-control-lg" id="charge-tax" placeholder="0.00" type="number" step="0.01" />
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

<script>
window.FolioFormConfig = {
    guestsUrl: '{{ route('reception.folio.guests') }}',
    searchUrl:  '{{ route('reception.folio.search') }}'
};
</script>
<script src="{{ asset('js/reception-folio-form.js') }}"></script>
