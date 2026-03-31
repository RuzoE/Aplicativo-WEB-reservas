@extends('layouts.app')

@php
    $adminView = true;
    $sidebarView = 'admin.mantenimiento-sidebar';
@endphp

@section('content')
<link rel="stylesheet" href="{{ asset('css/blade/admin/mantenimiento/index--style1.css') }}">

<div class="maintenance-container">

    <!-- Alertas -->
    @if ($errors->any())
        <div class="alert alert-danger" style="margin-bottom: 15px; border-radius: 8px; font-weight: 500;">
            <i class="bi bi-exclamation-triangle-fill"></i> <strong>Error:</strong> {{ $errors->first() }}
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success" style="margin-bottom: 15px; border-radius: 8px; font-weight: 500;">
            <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
        </div>
    @endif

    <div class="maintenance-header">
        <div class="maintenance-title">
            <i class="bi bi-tools"></i>
            Gestión de Mantenimiento
        </div>
        <button class="btn-create-order js-open-create-order-general" title="Nueva Orden General">
            <span class="btn-create-order-text">Nueva</span>
        </button>
    </div>

    <div class="divider-line"></div>

    <!-- Estadísticas -->
    <div class="stats-row">
        <div class="stat-card">
            <div class="icon"><i class="bi bi-wrench-adjustable"></i></div>
            <h3>{{ $activeCount }}</h3>
            <p>Órdenes Activas</p>
        </div>
        <div class="stat-card">
            <div class="icon"><i class="bi bi-exclamation-octagon-fill"></i></div>
            <h3>{{ $urgentCount }}</h3>
            <p>Órdenes Urgentes</p>
        </div>
        <div class="stat-card">
            <div class="icon"><i class="bi bi-door-open-fill"></i></div>
            <h3>{{ $totalRooms }}</h3>
            <p>Total Habitaciones</p>
        </div>
    </div>

    <!-- Tabla de Habitaciones -->
    @if ($individualRooms->isEmpty())
        <div class="maintenance-table-card">
            <div class="empty-state-container">
                <div class="empty-state-icon">
                    <i class="bi bi-inbox"></i>
                </div>
                <p class="empty-state-text">No hay habitaciones registradas en el sistema.</p>
            </div>
        </div>
    @else
        <div class="maintenance-table-card">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col" style="width: 80px;"><i class="bi bi-hash"></i> Hab.</th>
                            <th scope="col"><i class="bi bi-tags-fill"></i> Tipo</th>
                            <th scope="col"><i class="bi bi-info-circle-fill"></i> Estado</th>
                            <th scope="col"><i class="bi bi-flag-fill"></i> Prioridad</th>
                            <th scope="col" style="width: 200px;"><i class="bi bi-gear-fill"></i> Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($individualRooms as $room)
                            <tr>
                                <td>
                                    <span class="room-number">{{ $room->number }}</span>
                                </td>
                                <td>
                                    <span class="room-type">{{ $room->type_name }}</span>
                                </td>
                                <td>
                                    @if ($room->has_active_order)
                                        <span class="status-badge status-mantenimiento">
                                            <i class="bi bi-tools"></i> En Mant.
                                        </span>
                                    @else
                                        <span class="status-badge status-disponible">
                                            <i class="bi bi-check-circle-fill"></i> Disponible
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if ($room->has_active_order && $room->active_order)
                                        <span class="priority-badge priority-{{ $room->active_order->priority }}">
                                            @if ($room->active_order->priority === 'urgente')
                                                <i class="bi bi-star-fill"></i> {{ ucfirst($room->active_order->priority) }}
                                            @else
                                                {{ ucfirst($room->active_order->priority) }}
                                            @endif
                                        </span>
                                    @else
                                        <span style="color: #bbb;">—</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="actions-cell">
                                        @if ($room->has_active_order && $room->active_order && isset($room->active_order->id))
                                                <button class="btn-action btn-complete js-complete-order"
                                                    type="button"
                                                    title="Completar Mantenimiento"
                                                    data-order-id="{{ $room->active_order->id }}">
                                                <i class="bi bi-check-circle-fill"></i> Completar
                                            </button>
                                        @endif
                                        <button class="btn-action btn-history js-view-history" type="button" data-room-id="{{ $room->room_id }}" data-room-number="{{ $room->number }}" title="Ver Historial de esta habitación">
                                            <i class="bi bi-clock-history"></i> Historial
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Modal: Crear Orden de Trabajo -->
    <div id="createOrderModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="bi bi-plus-circle-fill"></i> Nueva Orden de Mantenimiento</h2>
                <button class="close-btn js-close-modal" type="button" data-modal-id="createOrderModal">&times;</button>
            </div>
            <form action="{{ route('admin.mantenimiento.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label>Habitación</label>
                    @php $availableRooms = $individualRooms->where('has_active_order', false)->values(); @endphp
                  <select class="form-control js-sync-room-type" id="modalRoomNumber" name="room_number" required @disabled($availableRooms->isEmpty())>
                        <option value="">Seleccione una habitación...</option>
                        @foreach ($availableRooms as $roomOption)
                      <option value="{{ $roomOption->number }}"
                          data-room-id="{{ $roomOption->room_id }}"
                          @selected((string) old('room_number') === (string) $roomOption->number)>
                                Habitación {{ $roomOption->number }} - {{ $roomOption->type_name }}
                            </option>
                        @endforeach
                    </select>
                  <input type="hidden" id="modalRoomId" name="room_id" value="{{ old('room_id') }}">
                </div>

                <div class="form-group">
                    <label>Descripción del Problema</label>
                  <textarea name="description" class="form-control" rows="4" required placeholder="Describe el problema a reparar...">{{ old('description') }}</textarea>
                </div>

                <div class="form-group">
                    <label>Prioridad</label>
                    <select name="priority" class="form-control" required>
                    <option value="normal" @selected(old('priority', 'normal') === 'normal')>Normal</option>
                    <option value="baja" @selected(old('priority') === 'baja')>Baja</option>
                    <option value="urgente" @selected(old('priority') === 'urgente')>Urgente</option>
                    </select>
                </div>

                <button type="submit" class="btn-submit">
                    <i class="bi bi-save-fill"></i> Guardar Orden
                </button>
            </form>
        </div>
    </div>

    <!-- Modal: Historial -->
    <div id="historyModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="bi bi-clock-history"></i> Historial - Hab. <span id="historyRoomNum"></span></h2>
                <button class="close-btn js-close-modal" type="button" data-modal-id="historyModal">&times;</button>
            </div>
            <div id="historyContent"></div>
        </div>
    </div>

    <!-- Modal: Confirm personalizado -->
    <div id="confirmModal" class="confirm-modal-overlay">
        <div class="confirm-modal-box">
            <div id="confirmIcon" class="confirm-modal-icon icon-warning">
                <i class="bi bi-question-circle-fill"></i>
            </div>
            <div id="confirmTitle" class="confirm-modal-title">¿Confirmar acción?</div>
            <div id="confirmMessage" class="confirm-modal-message">¿Está seguro de realizar esta acción?</div>
            <div class="confirm-modal-actions">
                <button class="confirm-modal-btn btn-confirm-cancel js-confirm-cancel" type="button">Cancelar</button>
                <button id="confirmAcceptBtn" class="confirm-modal-btn btn-confirm-accept js-confirm-accept" type="button">Aceptar</button>
            </div>
        </div>
    </div>

</div>

<script src="{{ asset('js/admin-mantenimiento.js') }}"></script>
@endsection
