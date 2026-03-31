@if ($history->isEmpty())
    <div class="text-center py-5" style="color: #adb5bd;">
        <i class="bi bi-inbox fs-1 d-block mb-3 opacity-25"></i>
        <p class="fs-5 fw-medium">No hay registros de mantenimiento para esta habitación</p>
    </div>
@else
    <div class="mb-4 pb-3 border-bottom d-none d-md-block">
        <h6 class="text-secondary text-uppercase fw-bold m-0" style="letter-spacing: 1px; font-size: 0.8rem;">
            <i class="bi bi-info-circle me-1"></i> RESUMEN DE ESTADO
        </h6>
        <div class="text-dark fw-bold h5 mb-0 mt-1">
            Habitación {{ $selectedRoomNumber ?: ($room->number ?? 'N/A') }} <span class="text-muted fw-normal">| {{ $room->roomtype->name ?? 'N/A' }}</span>
        </div>
    </div>

    <div class="history-timeline">
        @foreach ($history as $order)
            <div class="history-card mb-4">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <h5 class="mb-0 fw-bold text-dark">
                        <i class="bi bi-wrench-adjustable me-2 text-danger"></i>
                        {{ ucfirst($order->description) }}
                    </h5>
                    <span class="badge priority-{{ $order->priority }} px-3 py-2 rounded-pill">
                        @if ($order->priority === 'urgente')
                            <i class="bi bi-star-fill me-1"></i> Urgente
                        @else
                            {{ ucfirst($order->priority) }}
                        @endif
                    </span>
                </div>

                <div class="d-flex flex-wrap gap-3 mb-3">
                    @if ($order->status === 'completada')
                        <span class="status-indicator status-ready">
                            <i class="bi bi-check-circle-fill me-1"></i> Completada
                        </span>
                    @else
                        <span class="status-indicator status-pending">
                            <i class="bi bi-hourglass-split me-1"></i> {{ ucfirst($order->status) }}
                        </span>
                    @endif

                    <span class="text-muted small">
                        <i class="bi bi-calendar-plus me-1"></i>
                        <strong>Creada:</strong> {{ $order->created_at->format('d/m/Y H:i') }}
                    </span>

                    @if ($order->completed_at)
                        <span class="text-muted small">
                            <i class="bi bi-calendar-check me-1"></i>
                            <strong>Fin:</strong> {{ $order->completed_at->format('d/m/Y H:i') }}
                        </span>
                    @endif
                </div>

                @if ($order->notes)
                    <div class="notes-box p-3 rounded-3 mt-2">
                        <div class="small fw-bold text-uppercase text-secondary mb-1">Observaciones</div>
                        <p class="mb-0 text-dark" style="font-size: 0.95rem;">{{ $order->notes }}</p>
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    <link rel="stylesheet" href="{{ asset('css/blade/admin/mantenimiento/partials/history-list--style1.css') }}">
@endif


