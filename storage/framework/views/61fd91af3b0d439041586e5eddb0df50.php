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

    <div class="checkout-card">
        <h3>Procesar Salida</h3>
        <form id="checkout-form" class="mt-3">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold">ID de estancia</label>
                    <input id="co-stay" class="form-control form-control-lg" placeholder="Ingrese ID de estancia" />
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Confirmar salida</label>
                    <select id="co-confirm" class="form-select form-select-lg">
                        <option value="yes">Sí, procesar check-out</option>
                        <option value="no">No</option>
                    </select>
                </div>
                <div class="col-12 mt-4">
                    <button type="button" id="checkout-btn" class="btn-process-checkout">
                        <i class="bi bi-check-circle"></i> Procesar Check-out
                    </button>
                </div>
            </div>
        </form>

        <div id="checkout-result" class="mt-4"></div>
    </div>

    <div class="checkout-info-box">
        <h4><i class="bi bi-info-circle"></i> Proceso de Check-out</h4>
        <ul>
            <li>Verifica que el folio esté saldado completamente</li>
            <li>Genera la factura final del huésped</li>
            <li>Actualiza el estado de la habitación a "disponible"</li>
            <li>Cierra la estancia (stay) y marca fecha/hora de salida</li>
            <li>Libera la habitación para nuevas reservas</li>
        </ul>
    </div>
</div>
<?php /**PATH C:\laragon\www\hotel-piloto-sam\resources\views/reception/partials/checkout-form.blade.php ENDPATH**/ ?>