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
</style>

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
                    <label class="form-label fw-bold">ID de estancia</label>
                    <input id="folio-stay" class="form-control form-control-lg" placeholder="Ingrese ID de estancia" />
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

    <div class="action-columns">
        <div class="action-column">
            <h3 class="folio-section-title">
                <i class="bi bi-plus-circle"></i> Agregar Cargo
            </h3>
            <form id="add-charge">
                <div class="mb-3">
                    <label class="form-label fw-bold">Descripción</label>
                    <input class="form-control form-control-lg" id="charge-desc" placeholder="Descripción del cargo" />
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Monto</label>
                    <input class="form-control form-control-lg" id="charge-amount" placeholder="0.00" type="number" step="0.01" />
                </div>
                <button type="button" id="charge-btn" class="btn-add-charge">
                    <i class="bi bi-plus-lg"></i> Agregar Cargo
                </button>
            </form>
        </div>

        <div class="action-column">
            <h3 class="folio-section-title">
                <i class="bi bi-credit-card"></i> Registrar Pago
            </h3>
            <form id="add-payment">
                <div class="mb-3">
                    <label class="form-label fw-bold">Método de pago</label>
                    <input class="form-control form-control-lg" id="pay-method" placeholder="Efectivo, Tarjeta, etc." />
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Monto</label>
                    <input class="form-control form-control-lg" id="pay-amount" placeholder="0.00" type="number" step="0.01" />
                </div>
                <button type="button" id="pay-btn" class="btn-add-payment">
                    <i class="bi bi-check-lg"></i> Registrar Pago
                </button>
            </form>
        </div>
    </div>
</div>
<?php /**PATH C:\laragon\www\hotel-piloto-sam\resources\views/reception/partials/folio-form.blade.php ENDPATH**/ ?>