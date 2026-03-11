@extends('layouts.app')

@php
    $adminView = true;
    $sidebarView = 'admin.mantenimiento-sidebar';
@endphp

@section('content')
<style>
  .maintenance-container {
    padding: 40px 20px;
    background: #f5f5f5;
    min-height: 100vh;
  }
  .maintenance-header {
    margin-bottom: 40px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 20px;
  }
  .maintenance-title {
    font-size: 2rem;
    font-weight: 700;
    color: #333;
    display: flex;
    align-items: center;
    gap: 12px;
  }
  .maintenance-title i {
    color: #ff6b6b;
    font-size: 1.8rem;
  }
  .stats-row {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 20px;
      margin-bottom: 30px;
  }
  .stat-card {
      background: white;
      border-radius: 12px;
      padding: 20px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.08);
      border-left: 4px solid #ff6b6b;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      transition: transform 0.2s, box-shadow 0.2s;
  }
  .stat-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 4px 12px rgba(0,0,0,0.12);
  }
  .stat-card .icon {
      font-size: 2.5rem;
      color: #ff6b6b;
      margin-bottom: 10px;
  }
  .stat-card h3 {
      font-size: 2rem;
      font-weight: 800;
      color: #333;
      margin: 0 0 5px 0;
  }
  .stat-card p {
      margin: 0;
      color: #666;
      font-weight: 600;
      text-transform: uppercase;
      font-size: 0.8rem;
      letter-spacing: 0.5px;
  }
  .btn-create-order {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%);
    color: white !important;
    text-decoration: none;
    font-size: 1.5rem;
    transition: all 0.2s;
    box-shadow: 0 4px 12px rgba(238, 90, 82, 0.3);
    position: relative;
    overflow: hidden;
    cursor: pointer;
    border: none;
  }
  .btn-create-order i {
    color: white !important;
    font-size: 1.8rem !important;
  }
  .btn-create-order-text {
    font-weight: 700;
    font-size: 0.75rem;
    color: white;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    line-height: 1;
  }
  .btn-create-order:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(238, 90, 82, 0.4);
    color: white;
  }
  .maintenance-table-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    overflow: hidden;
  }
  .table {
    margin-bottom: 0;
  }
  .table thead {
    background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%);
    color: white;
  }
  .table thead th {
    padding: 18px 16px;
    font-weight: 600;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border: none;
    vertical-align: middle;
  }
  .table thead th i {
    margin-right: 6px;
    opacity: 0.9;
  }
  .table tbody tr {
    border-bottom: 1px solid #e8e8e8;
    transition: all 0.2s;
  }
  .table tbody tr:hover {
    background-color: #f9f9f9;
    box-shadow: inset 0 0 8px rgba(238, 90, 82, 0.08);
  }
  .table tbody td {
    padding: 16px;
    vertical-align: middle;
    color: #333;
    font-size: 0.95rem;
  }
  .room-number {
    font-weight: 700;
    color: #ff6b6b;
    font-size: 0.95rem;
  }
  .room-type {
    font-weight: 600;
    color: #666;
    font-size: 0.85rem;
  }
  .status-badge {
    display: inline-block;
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 0.8rem;
    font-weight: 700;
    text-align: center;
    white-space: nowrap;
  }
  .status-disponible {
    background: #d4edda;
    color: #155724;
  }
  .status-mantenimiento {
    background: #fff3cd;
    color: #856404;
  }
  .status-ocupada {
    background: #f8d7da;
    color: #721c24;
  }
  .priority-badge {
    display: inline-block;
    padding: 5px 10px;
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    white-space: nowrap;
  }
  .priority-baja {
    background: #e2e3e5;
    color: #383d41;
  }
  .priority-normal {
    background: #d1ecf1;
    color: #0c5460;
  }
  .priority-urgente {
    background: #f8d7da;
    color: #721c24;
  }
  .actions-cell {
    display: flex;
    gap: 6px;
    align-items: center;
    flex-wrap: wrap;
  }
  .btn-action {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    padding: 6px 12px;
    border-radius: 6px;
    border: none;
    cursor: pointer;
    transition: all 0.2s;
    font-size: 0.75rem;
    font-weight: 600;
    text-decoration: none;
    white-space: nowrap;
  }
  .btn-action i {
    font-size: 0.85rem;
  }
  .btn-mant {
    background: white;
    color: #ff6b6b;
    border: 1.5px solid #ff6b6b;
  }
  .btn-mant:hover {
    background: #ff6b6b;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(255, 107, 107, 0.3);
  }
  .btn-urgent {
    background: linear-gradient(135deg, #FFC107 0%, #FF9800 100%);
    color: white;
    box-shadow: 0 2px 6px rgba(255, 193, 7, 0.3);
  }
  .btn-urgent:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(255, 193, 7, 0.4);
    color: white;
  }
  .btn-complete {
    background: linear-gradient(135deg, #4CAF50 0%, #388E3C 100%);
    color: white;
    box-shadow: 0 2px 6px rgba(76, 175, 80, 0.3);
  }
  .btn-complete:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(76, 175, 80, 0.4);
    color: white;
  }
  .btn-history {
    background: linear-gradient(135deg, #607D8B 0%, #455A64 100%);
    color: white;
    box-shadow: 0 2px 6px rgba(96, 125, 139, 0.3);
  }
  .btn-history:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(96, 125, 139, 0.4);
    color: white;
  }
  .empty-state-container {
    text-align: center;
    padding: 80px 40px;
  }
  .empty-state-icon {
    font-size: 4rem;
    color: #e0e0e0;
    margin-bottom: 20px;
  }
  .empty-state-text {
    font-size: 1.1rem;
    color: #999;
    margin-bottom: 10px;
  }
  .divider-line {
    width: 100%;
    height: 2px;
    background: linear-gradient(90deg, #ff6b6b 0%, #ee5a52 100%);
    margin: 20px 0 30px;
    border-radius: 2px;
  }

  /* Modal Styling Update */
  .modal {
      display: none;
      position: fixed;
      z-index: 1050;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0,0,0,0.5);
      padding: 40px 20px;
      backdrop-filter: blur(4px);
  }
  .modal.show {
      display: flex;
      align-items: center;
      justify-content: center;
  }
  .modal-content {
      background: white;
      border-radius: 12px;
      width: 100%;
      max-width: 600px;
      padding: 30px;
      box-shadow: 0 10px 40px rgba(0,0,0,0.2);
      max-height: 90vh;
      overflow-y: auto;
      border: none;
      animation: modalFadeIn 0.3s ease-out;
  }
  @keyframes modalFadeIn {
      from { opacity: 0; transform: translateY(-20px); }
      to { opacity: 1; transform: translateY(0); }
  }
  .modal-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 25px;
      border-bottom: 2px solid #ff6b6b;
      padding-bottom: 15px;
  }
  .modal-header h2 {
      color: #333;
      margin: 0;
      font-size: 1.5rem;
      font-weight: 700;
  }
  .modal-header h2 i {
      color: #ff6b6b;
      margin-right: 10px;
  }
  .close-btn {
      background: none;
      border: none;
      font-size: 1.8rem;
      cursor: pointer;
      color: #999;
      transition: color 0.2s;
  }
  .close-btn:hover {
      color: #ff6b6b;
  }
  .form-group {
      margin-bottom: 20px;
  }
  .form-group label {
      display: block;
      margin-bottom: 8px;
      font-weight: 600;
      color: #444;
  }
  .form-control {
      width: 100%;
      padding: 12px 15px;
      border: 1px solid #ddd;
      border-radius: 8px;
      font-size: 1rem;
      font-family: inherit;
      transition: border-color 0.2s, box-shadow 0.2s;
  }
  .form-control:focus {
      outline: none;
      border-color: #ff6b6b;
      box-shadow: 0 0 0 3px rgba(255, 107, 107, 0.15);
  }
  .btn-submit {
      background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%);
      color: white;
      padding: 12px 25px;
      border: none;
      border-radius: 8px;
      font-weight: 600;
      cursor: pointer;
      width: 100%;
      transition: all 0.3s;
      font-size: 1.05rem;
      margin-top: 10px;
  }
  .btn-submit:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 14px rgba(255, 107, 107, 0.3);
  }

  /* Custom Confirm Modal */
  .confirm-modal-overlay {
    display: none;
    position: fixed;
    z-index: 1100;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
    backdrop-filter: blur(4px);
    align-items: center;
    justify-content: center;
    padding: 20px;
  }
  .confirm-modal-overlay.show {
    display: flex;
  }
  .confirm-modal-box {
    background: white;
    border-radius: 16px;
    width: 100%;
    max-width: 420px;
    padding: 30px;
    box-shadow: 0 20px 60px rgba(0,0,0,0.25);
    text-align: center;
    animation: modalFadeIn 0.3s ease-out;
  }
  .confirm-modal-icon {
    font-size: 3rem;
    margin-bottom: 15px;
  }
  .confirm-modal-icon.icon-warning {
    color: #FFC107;
  }
  .confirm-modal-icon.icon-danger {
    color: #ff6b6b;
  }
  .confirm-modal-icon.icon-success {
    color: #4CAF50;
  }
  .confirm-modal-icon.icon-info {
    color: #2196F3;
  }
  .confirm-modal-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: #333;
    margin-bottom: 8px;
  }
  .confirm-modal-message {
    font-size: 0.95rem;
    color: #666;
    margin-bottom: 25px;
    line-height: 1.5;
  }
  .confirm-modal-actions {
    display: flex;
    gap: 12px;
    justify-content: center;
  }
  .confirm-modal-btn {
    padding: 10px 24px;
    border-radius: 8px;
    border: none;
    font-weight: 600;
    font-size: 0.95rem;
    cursor: pointer;
    transition: all 0.2s;
    min-width: 120px;
  }
  .confirm-modal-btn:hover {
    transform: translateY(-2px);
  }
  .btn-confirm-cancel {
    background: #f0f0f0;
    color: #666;
  }
  .btn-confirm-cancel:hover {
    background: #e0e0e0;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
  }
  .btn-confirm-accept {
    background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%);
    color: white;
    box-shadow: 0 4px 12px rgba(238, 90, 82, 0.3);
  }
  .btn-confirm-accept:hover {
    box-shadow: 0 6px 16px rgba(238, 90, 82, 0.4);
  }
  .btn-confirm-accept.btn-green {
    background: linear-gradient(135deg, #4CAF50 0%, #388E3C 100%);
    box-shadow: 0 4px 12px rgba(76, 175, 80, 0.3);
  }
  .btn-confirm-accept.btn-green:hover {
    box-shadow: 0 6px 16px rgba(76, 175, 80, 0.4);
  }
  .btn-confirm-accept.btn-yellow {
    background: linear-gradient(135deg, #FFC107 0%, #FF9800 100%);
    box-shadow: 0 4px 12px rgba(255, 193, 7, 0.3);
  }
  .btn-confirm-accept.btn-yellow:hover {
    box-shadow: 0 6px 16px rgba(255, 193, 7, 0.4);
  }

  @media (max-width: 768px) {
    .maintenance-header {
      flex-direction: column;
      align-items: flex-start;
    }
    .stats-row {
      grid-template-columns: 1fr;
    }
    .table thead th {
      padding: 12px 8px;
      font-size: 0.75rem;
    }
    .table tbody td {
      padding: 12px 8px;
      font-size: 0.85rem;
    }
    .actions-cell {
      flex-direction: row;
      flex-wrap: wrap;
    }
  }
</style>

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
        <button class="btn-create-order" onclick="openCreateOrderModalGeneral()" title="Nueva Orden General">
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
                                            <button class="btn-action btn-complete"
                                                    onclick="completeOrder({{ $room->active_order->id }})"
                                                    title="Completar Mantenimiento"
                                                    data-order-id="{{ $room->active_order->id }}">
                                                <i class="bi bi-check-circle-fill"></i> Completar
                                            </button>
                                        @endif
                                        <button class="btn-action btn-history" onclick="viewHistory({{ $room->room_id }}, '{{ $room->number }}')" title="Ver Historial de esta habitación">
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
                <button class="close-btn" onclick="closeModal('createOrderModal')">&times;</button>
            </div>
            <form action="{{ route('admin.mantenimiento.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label>Habitación</label>
                    @php $availableRooms = $individualRooms->where('has_active_order', false)->values(); @endphp
                  <select class="form-control" id="modalRoomNumber" name="room_number" required @disabled($availableRooms->isEmpty()) onchange="syncSelectedRoomTypeId()">
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
                <button class="close-btn" onclick="closeModal('historyModal')">&times;</button>
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
                <button class="confirm-modal-btn btn-confirm-cancel" onclick="closeConfirmModal()">Cancelar</button>
                <button id="confirmAcceptBtn" class="confirm-modal-btn btn-confirm-accept" onclick="confirmAction()">Aceptar</button>
            </div>
        </div>
    </div>

</div>

<script src="{{ asset('js/admin-mantenimiento.js') }}"></script>
@endsection
{{-- NOTE: script block below has been moved to public/js/admin-mantenimiento.js --}}
{{-- Original inline block kept as reference marker ONLY — DO NOT UNCOMMENT
<script>
    let pendingConfirmCallback = null;

    function showConfirmModal(options) {
        console.log('📋 showConfirmModal llamada con opciones:', options);
        const { title, message, icon, iconClass, btnClass, btnText, onConfirm } = options;
        document.getElementById('confirmTitle').textContent = title || '¿Confirmar acción?';
        document.getElementById('confirmMessage').textContent = message || '¿Está seguro?';

        const iconEl = document.getElementById('confirmIcon');
        iconEl.className = 'confirm-modal-icon ' + (iconClass || 'icon-warning');
        iconEl.innerHTML = '<i class="bi ' + (icon || 'bi-question-circle-fill') + '"></i>';

        const acceptBtn = document.getElementById('confirmAcceptBtn');
        acceptBtn.className = 'confirm-modal-btn btn-confirm-accept ' + (btnClass || '');
        acceptBtn.textContent = btnText || 'Aceptar';

        pendingConfirmCallback = onConfirm;
        console.log('✅ Callback guardado:', typeof pendingConfirmCallback);
        document.getElementById('confirmModal').classList.add('show');
    }

    function closeConfirmModal() {
        document.getElementById('confirmModal').classList.remove('show');
        pendingConfirmCallback = null;
    }

    function confirmAction() {
        console.log('🎯 confirmAction llamada');
        console.log('📞 Callback pendiente:', pendingConfirmCallback ? 'SÍ' : 'NO');

        // Guardar el callback ANTES de cerrar el modal
        const callback = pendingConfirmCallback;
        closeConfirmModal();

        if (callback) {
            console.log('🚀 Ejecutando callback...');
            callback();
        } else {
            console.warn('⚠️ No hay callback pendiente!');
        }
    }

    function openCreateOrderModal(roomId, roomNum) {
        const roomSelect = document.getElementById('modalRoomNumber');
        roomSelect.value = roomNum;
        syncSelectedRoomTypeId();
        document.getElementById('createOrderModal').classList.add('show');
    }

    function openCreateOrderModalGeneral() {
        // Usar el modal de crear orden directamente con un selector de habitaciones
        document.getElementById('modalRoomNumber').value = '';
        document.getElementById('modalRoomId').value = '';
        document.getElementById('createOrderModal').classList.add('show');
    }

    function syncSelectedRoomTypeId() {
        const roomSelect = document.getElementById('modalRoomNumber');
        const selectedOption = roomSelect.options[roomSelect.selectedIndex];
        const roomId = selectedOption ? selectedOption.getAttribute('data-room-id') : '';
        document.getElementById('modalRoomId').value = roomId || '';
    }

    syncSelectedRoomTypeId();

    function closeModal(modalId) {
        document.getElementById(modalId).classList.remove('show');
    }

    function completeOrder(orderId) {
        console.log('🔧 completeOrder llamada con ID:', orderId);

        showConfirmModal({
            title: 'Completar Mantenimiento',
            message: '¿Está seguro de marcar esta orden como completada?',
            icon: 'bi-check-circle-fill',
            iconClass: 'icon-success',
            btnClass: 'btn-green',
            btnText: 'Sí, Completar',
            onConfirm: function() {
                console.log('✅ Confirmación aceptada, iniciando fetch...');
                console.log('📝 Order ID:', orderId);

                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                console.log('🔑 CSRF Token:', csrfToken ? 'Encontrado' : 'NO ENCONTRADO');

                if (!csrfToken) {
                    alert('Error: No se encontró el token CSRF');
                    return;
                }

                const url = `/admin/mantenimiento/${orderId}/complete`;
                console.log('🌐 URL:', url);

                fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    console.log('📡 Respuesta recibida - Status:', response.status);
                    if (!response.ok) {
                        return response.text().then(text => {
                            console.error('❌ Error response:', text);
                            throw new Error(`HTTP ${response.status}: ${text.substring(0, 200)}`);
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('✅ Success:', data);
                  location.reload();
                })
                .catch(err => {
                    console.error('❌ Error completo:', err);
                    alert('Error al completar el mantenimiento: ' + err.message);
                });
            }
        });
    }

    function markUrgent(orderId) {
        showConfirmModal({
            title: 'Marcar como Urgente',
            message: '¿Desea aumentar la prioridad de esta orden a urgente?',
            icon: 'bi-exclamation-triangle-fill',
            iconClass: 'icon-warning',
            btnClass: 'btn-yellow',
            btnText: 'Sí, Marcar Urgente',
            onConfirm: function() {
                fetch(`/admin/mantenimiento/${orderId}/urgent`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    if (!response.ok) throw new Error('Error en la solicitud');
                    return response.json();
                })
                .then(() => location.reload())
                .catch(err => {
                    console.error(err);
                    location.reload();
                });
            }
        });
    }

    function viewHistory(roomId, roomNum) {
        document.getElementById('historyRoomNum').textContent = roomNum;
        fetch(`/admin/mantenimiento/room/${roomId}/history`)
            .then(r => r.text())
            .then(html => {
                document.getElementById('historyContent').innerHTML = html;
                document.getElementById('historyModal').classList.add('show');
            });
    }

    window.onclick = function(event) {
        const createModal = document.getElementById('createOrderModal');
        const historyModal = document.getElementById('historyModal');
        const confirmModal = document.getElementById('confirmModal');
        if (event.target === createModal) {
            createModal.classList.remove('show');
        }
        if (event.target === historyModal) {
            historyModal.classList.remove('show');
        }
        if (event.target === confirmModal) {
            closeConfirmModal();
        }
    };

    // Función de prueba directa (sin modal) - Llamar desde consola: testCompleteOrder(1)
--}}
