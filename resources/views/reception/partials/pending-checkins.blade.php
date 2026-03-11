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
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th style="color: #2196F3; font-weight: 700;">
                            <i class="bi bi-hash"></i> Codigo
                        </th>
                        <th style="color: #2196F3; font-weight: 700;">
                            <i class="bi bi-person"></i> Nombre del Huesped
                        </th>
                        <th style="color: #2196F3; font-weight: 700;">
                            <i class="bi bi-envelope"></i> Email
                        </th>
                        <th style="color: #2196F3; font-weight: 700;">
                            <i class="bi bi-calendar-event"></i> Llegada
                        </th>
                        <th style="color: #2196F3; font-weight: 700;">
                            <i class="bi bi-calendar-check"></i> Salida
                        </th>
                        <th style="color: #2196F3; font-weight: 700;">
                            <i class="bi bi-cash-coin"></i> Total
                        </th>
                        <th style="color: #2196F3; font-weight: 700;">
                            <i class="bi bi-gear"></i> Accion
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pendingCheckIns as $reservation)
                        <tr class="reservation-row" style="border-left: 4px solid #FFC107;">
                            <td>
                                <span class="badge bg-warning text-dark">
                                    RES-{{ str_pad($reservation->id, 6, '0', STR_PAD_LEFT) }}
                                </span>
                            </td>
                            <td>
                                <strong>{{ $reservation->user->name ?? 'Sin nombre' }}</strong>
                            </td>
                            <td>
                                <a href="mailto:{{ $reservation->user->email }}">
                                    {{ $reservation->user->email ?? 'N/A' }}
                                </a>
                            </td>
                            <td>
                                <small class="d-block text-muted">
                                    <i class="bi bi-calendar"></i>
                                    {{ $reservation->check_in->format('d/m/Y') }}
                                </small>
                                <small class="d-block text-success">
                                    <i class="bi bi-clock"></i>
                                    {{ $reservation->check_in->format('H:i') }}
                                </small>
                            </td>
                            <td>
                                <small class="d-block text-muted">
                                    <i class="bi bi-calendar"></i>
                                    {{ $reservation->check_out->format('d/m/Y') }}
                                </small>
                                <small class="d-block">
                                    ({{ $reservation->stayDays }} noches)
                                </small>
                            </td>
                            <td>
                                <strong class="text-primary">
                                    ${{ number_format($reservation->stayDays * ($reservation->room->price ?? 0), 0, ',', '.') }}
                                </strong>
                            </td>
                            <td>
                                <a href="{{ route('reception.checkin.show', $reservation->id) }}"
                                   class="btn btn-sm btn-success"
                                   title="Procesar check-in">
                                    <i class="bi bi-door-open"></i> Procesar
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <style>
            .pending-checkins-section {
                background: white;
                border-radius: 12px;
                padding: 30px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.1);
                margin-bottom: 40px;
            }

            .section-header {
                border-bottom: 2px solid #FFC107;
                padding-bottom: 15px;
            }

            .table {
                margin-bottom: 0;
            }

            .table thead {
                background: linear-gradient(135deg, #2196F3 0%, #1565C0 100%);
            }

            .table thead th {
                color: white !important;
                border: none;
                padding: 15px;
                font-weight: 600;
                text-transform: uppercase;
                font-size: 0.85rem;
                letter-spacing: 0.5px;
            }

            .table tbody tr {
                transition: all 0.3s ease;
                border-bottom: 1px solid #f0f0f0;
            }

            .table tbody tr:hover {
                background-color: #f8f9fa;
                box-shadow: inset 0 0 10px rgba(33, 150, 243, 0.08);
            }

            .table tbody td {
                padding: 15px;
                vertical-align: middle;
                color: #333;
            }

            .btn-success {
                background: linear-gradient(135deg, #4CAF50 0%, #2E7D32 100%);
                border: none;
                box-shadow: 0 2px 8px rgba(76, 175, 80, 0.3);
                transition: all 0.2s;
            }

            .btn-success:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(76, 175, 80, 0.4);
            }

            .reservation-row {
                cursor: pointer;
            }
        </style>
    @endif
</div>
