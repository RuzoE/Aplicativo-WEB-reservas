<div class="pending-checkins-section">
    <div class="section-header mb-4">
        <h2 style="color: #333; margin-bottom: 20px; font-weight: 700;">
            <i class="bi bi-door-open"></i> Reservas Pendientes de Check-in
        </h2>
        <p class="text-muted">{{ $pendingCheckIns->count() }} reserva(s) esperando ser procesada(s)</p>
    </div>

    @if($pendingCheckIns->isEmpty())
        <div class="alert alert-info alert-dismissible fade show mb-4" role="alert">
            <i class="bi bi-info-circle"></i> No hay reservas pendientes de check-in en este momento.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-hover align-middle custom-pending-table">
                <thead>
                    <tr>
                        <th><i class="bi bi-hash"></i> Codigo</th>
                        <th><i class="bi bi-person"></i> Huésped</th>
                        <th class="d-none d-md-table-cell"><i class="bi bi-envelope"></i> Email</th>
                        <th><i class="bi bi-calendar-event"></i> Estancia</th>
                        <th><i class="bi bi-cash-coin"></i> Total</th>
                        <th><i class="bi bi-gear"></i> Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pendingCheckIns as $reservation)
                        <tr class="reservation-row">
                            <td data-label="Código">
                                <span class="badge bg-warning text-dark px-2 py-1">
                                    RES-{{ str_pad($reservation->id, 6, '0', STR_PAD_LEFT) }}
                                </span>
                            </td>
                            <td data-label="Huésped">
                                <div class="fw-bold">{{ $reservation->nombre_cliente ?: ($reservation->user->name ?? 'Sin nombre') }}</div>
                                <div class="text-muted d-md-none small">{{ $reservation->user->email ?? '' }}</div>
                            </td>
                            <td data-label="Email" class="d-none d-md-table-cell">
                                <span class="text-muted small">{{ $reservation->user->email ?? 'N/A' }}</span>
                            </td>
                            <td data-label="Estancia">
                                <div class="small">
                                    <span class="text-primary fw-bold">{{ $reservation->check_in->format('d/m/Y') }}</span>
                                    <span class="mx-1 text-muted">→</span>
                                    <span class="text-muted">{{ $reservation->check_out->format('d/m/Y') }}</span>
                                </div>
                                <div class="text-success x-small">({{ $reservation->stayDays }} noches)</div>
                            </td>
                            <td data-label="Total">
                                <div class="fw-black text-dark">
                                    ${{ number_format($reservation->total_amount, 0, ',', '.') }}
                                </div>
                            </td>
                            <td data-label="Acción">
                                <a href="{{ route('reception.checkin.show', $reservation->id) }}"
                                   class="btn btn-sm btn-primary px-3 rounded-pill shadow-sm"
                                   title="Procesar check-in">
                                    <i class="bi bi-door-open-fill me-1"></i> Check-in
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <link rel="stylesheet" href="{{ asset('css/blade/reception/partials/pending-checkins--style1.css') }}">
    @endif
</div>


