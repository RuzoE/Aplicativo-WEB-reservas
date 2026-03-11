<style>
    .checkout-section {
        padding: 40px 60px;
        max-width: 100%;
    }

    .checkout-header {
        margin-bottom: 30px;
        border-bottom: 3px solid #2196F3;
        padding-bottom: 20px;
    }

    .checkout-header h2 {
        color: #2196F3;
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 10px;
    }

    .checkout-header p {
        color: #666;
        font-size: 1.1rem;
        margin: 0;
    }

    .checkout-card {
        background: white;
        border-radius: 12px;
        padding: 30px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        margin-bottom: 30px;
    }

    .checkout-card h3 {
        color: #333;
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 15px;
    }

    .btn-process-checkout {
        background: linear-gradient(135deg, #9C27B0 0%, #6A1B9A 100%);
        color: white;
        border: none;
        padding: 12px 30px;
        border-radius: 8px;
        font-weight: 600;
        transition: transform 0.3s, box-shadow 0.3s;
        box-shadow: 0 4px 12px rgba(156, 39, 176, 0.3);
    }

    .btn-process-checkout:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(156, 39, 176, 0.4);
        color: white;
    }

    .checkout-info-box {
        background: #f3e5f5;
        border-left: 4px solid #9C27B0;
        padding: 20px;
        border-radius: 8px;
        margin-top: 20px;
    }

    .checkout-info-box h4 {
        color: #9C27B0;
        font-weight: 700;
        margin-bottom: 10px;
    }

    .checkout-info-box ul {
        margin: 0;
        padding-left: 20px;
        color: #666;
    }
</style>

<div class="checkout-section">
    <div class="checkout-header">
        <h2><i class="bi bi-box-arrow-in-right"></i> Check-out</h2>
        <p>Procesa la salida de huéspedes y genera la factura final</p>
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

        <!-- Formulario de Pago (Si hay saldo) -->
        <div id="co-payment-section" class="checkout-card mb-4" style="display:none; border: 2px solid #FF9800;">
            <h4 class="text-warning mb-3"><i class="bi bi-wallet2"></i> Registrar Pago Pendiente</h4>
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label fw-bold">Método de Pago</label>
                    <select id="co-pay-method" class="form-select text-dark">
                        <option value="Efectivo">Efectivo</option>
                        <option value="Tarjeta de Crédito">Tarjeta de Crédito</option>
                        <option value="Tarjeta de Débito">Tarjeta de Débito</option>
                        <option value="Transferencia">Transferencia</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Monto a Pagar</label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" id="co-pay-amount" class="form-control" step="0.01" min="0">
                    </div>
                </div>
                <div class="col-md-4">
                    <button type="button" id="co-btn-pay" class="btn btn-warning w-100 fw-bold">
                        Registrar Pago
                    </button>
                </div>
            </div>
            <div id="co-pay-msg" class="mt-2"></div>
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

<script>
window.CheckoutFormConfig = {
    folioSearchUrl: '{{ route('reception.folio.search') }}',
    dashboardUrl:   '{{ route('reception.dashboard') }}'
};
</script>
<script src="{{ asset('js/reception-checkout-form.js') }}"></script>
