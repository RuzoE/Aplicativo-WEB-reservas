<link rel="stylesheet" href="{{ asset('css/blade/reception/partials/checkin-form--style1.css') }}">

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

    <div class="checkin-header d-flex justify-content-between align-items-center">
        <div>
            <h2><i class="bi bi-door-open"></i> Registrarse</h2>
            <p>Selecciona una reserva pendiente para completar el check-in</p>
        </div>
        <div>
            <a href="{{ route('reception.walkin.create') }}" class="btn btn-primary shadow-sm" style="font-weight: 600; padding: 10px 20px; border-radius: 8px; background: #2196F3; border: none;">
                <i class="bi bi-person-plus-fill me-2"></i> Nuevo Registro Directo (Walk-In)
            </a>
        </div>
    </div>

    @include('components.reception.pending-checkins')
</div>



