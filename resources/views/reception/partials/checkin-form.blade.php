<style>
    .checkin-section {
        padding: 40px 60px;
        max-width: 100%;
    }

    .checkin-header {
        margin-bottom: 30px;
        border-bottom: 3px solid #2196F3;
        padding-bottom: 20px;
    }

    .checkin-header h2 {
        color: #2196F3;
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 10px;
    }

    .checkin-header p {
        color: #666;
        font-size: 1.1rem;
        margin: 0;
    }

    .success-notification {
        background: linear-gradient(135deg, #4CAF50 0%, #2E7D32 100%);
        color: white;
        padding: 20px 25px;
        border-radius: 12px;
        margin-bottom: 30px;
        box-shadow: 0 4px 12px rgba(76, 175, 80, 0.3);
        border-left: 5px solid #1B7A27;
        display: flex;
        align-items: center;
        gap: 15px;
        animation: slideDown 0.4s ease-out;
    }

    .success-notification i {
        font-size: 1.8rem;
    }

    .success-notification h4 {
        margin: 0 0 5px 0;
        font-weight: 700;
        font-size: 1.1rem;
    }

    .success-notification p {
        margin: 0;
        font-size: 0.95rem;
        opacity: 0.95;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>

<div class="checkin-section">
    @if(session('success'))
        <div class="success-notification">
            <i class="bi bi-check-circle"></i>
            <div>
                <h4>¡Operación Exitosa!</h4>
                <p>{{ session('success') }}</p>
            </div>
        </div>
    @endif

    <div class="checkin-header">
        <h2><i class="bi bi-door-open"></i> Registrarse</h2>
        <p>Selecciona una reserva pendiente para completar el check-in</p>
    </div>

    @include('reception.partials.pending-checkins')
</div>
