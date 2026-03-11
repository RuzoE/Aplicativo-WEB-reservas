// Mantenimiento module: confirm modal, CRUD actions, history viewer
// These functions are declared at global scope because HTML onclick= attributes reference them directly.

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
        onConfirm: function () {
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

window.onclick = function (event) {
    const createModal  = document.getElementById('createOrderModal');
    const historyModal = document.getElementById('historyModal');
    const confirmModal = document.getElementById('confirmModal');
    if (event.target === createModal)  createModal.classList.remove('show');
    if (event.target === historyModal) historyModal.classList.remove('show');
    if (event.target === confirmModal) closeConfirmModal();
};

// Función de prueba directa (sin modal) — desde consola: testCompleteOrder(1)
window.testCompleteOrder = function (orderId) {
    console.log('🧪 TEST DIRECTO - Completando orden:', orderId);

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    console.log('🔑 Token:', csrfToken ? 'OK' : 'FALTA');

    if (!csrfToken) {
        console.error('❌ No hay CSRF token');
        return;
    }

    const url = `/admin/mantenimiento/${orderId}/complete`;
    console.log('🌐 Llamando a:', url);

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
        console.log('📡 Status:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('✅ Respuesta del servidor:', data);
        alert('Test exitoso! Recargando página...');
        setTimeout(() => location.reload(), 1000);
    })
    .catch(err => {
        console.error('❌ Error en test:', err);
        alert('Error en test: ' + err.message);
    });
};
