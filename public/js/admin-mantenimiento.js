// Mantenimiento module: confirm modal, CRUD actions, history viewer.
// Functions remain in global scope because delegated handlers call window.* methods.

let pendingConfirmCallback = null;

function showConfirmModal(options) {
    const { title, message, icon, iconClass, btnClass, btnText, onConfirm } = options;
    document.getElementById('confirmTitle').textContent = title || '¿Confirmar acción?';
    document.getElementById('confirmMessage').textContent = message || '¿Está seguro?';

    const iconEl = document.getElementById('confirmIcon');
    iconEl.className = 'confirm-modal-icon ' + (iconClass || 'icon-warning');
    iconEl.innerHTML = '<i class="bi ' + (icon || 'bi-question-circle-fill') + '"></i>';

    const acceptBtn = document.getElementById('confirmAcceptBtn');
    acceptBtn.className = 'confirm-modal-btn btn-confirm-accept js-confirm-accept ' + (btnClass || '');
    acceptBtn.textContent = btnText || 'Aceptar';

    pendingConfirmCallback = onConfirm;
    document.getElementById('confirmModal').classList.add('show');
}

function closeConfirmModal() {
    document.getElementById('confirmModal').classList.remove('show');
    pendingConfirmCallback = null;
}

function confirmAction() {
    const callback = pendingConfirmCallback;
    closeConfirmModal();

    if (callback) {
        callback();
    }
}

function openCreateOrderModal(roomId, roomNum) {
    const roomSelect = document.getElementById('modalRoomNumber');
    roomSelect.value = roomNum;
    syncSelectedRoomTypeId();
    document.getElementById('createOrderModal').classList.add('show');
}

function openCreateOrderModalGeneral() {
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
    showConfirmModal({
        title: 'Completar Mantenimiento',
        message: '¿Está seguro de marcar esta orden como completada?',
        icon: 'bi-check-circle-fill',
        iconClass: 'icon-success',
        btnClass: 'btn-green',
        btnText: 'Sí, Completar',
        onConfirm: function () {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

            if (!csrfToken) {
                alert('Error: No se encontró el token CSRF');
                return;
            }

            fetch(`/admin/mantenimiento/${orderId}/complete`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
                .then(response => {
                    if (!response.ok) {
                        return response.text().then(text => {
                            throw new Error(`HTTP ${response.status}: ${text.substring(0, 200)}`);
                        });
                    }
                    return response.json();
                })
                .then(() => location.reload())
                .catch(err => alert('Error al completar el mantenimiento: ' + err.message));
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
        onConfirm: function () {
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
                .catch(() => location.reload());
        }
    });
}

function viewHistory(roomId, roomNum) {
    document.getElementById('historyRoomNum').textContent = roomNum;
    const historyUrl = `/admin/mantenimiento/room/${roomId}/history?room_number=${encodeURIComponent(roomNum)}`;
    fetch(historyUrl, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
        .then(r => r.text())
        .then(html => {
            document.getElementById('historyContent').innerHTML = html;
            document.getElementById('historyModal').classList.add('show');
        });
}

window.onclick = function (event) {
    const createModal = document.getElementById('createOrderModal');
    const historyModal = document.getElementById('historyModal');
    const confirmModal = document.getElementById('confirmModal');
    if (event.target === createModal) createModal.classList.remove('show');
    if (event.target === historyModal) historyModal.classList.remove('show');
    if (event.target === confirmModal) closeConfirmModal();
};

window.showConfirmModal = showConfirmModal;
window.closeConfirmModal = closeConfirmModal;
window.confirmAction = confirmAction;
window.openCreateOrderModal = openCreateOrderModal;
window.openCreateOrderModalGeneral = openCreateOrderModalGeneral;
window.syncSelectedRoomTypeId = syncSelectedRoomTypeId;
window.closeModal = closeModal;
window.completeOrder = completeOrder;
window.markUrgent = markUrgent;
window.viewHistory = viewHistory;
