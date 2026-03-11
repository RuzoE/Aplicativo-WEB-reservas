// Reception folio form: guest list, folio search, charges, payments
// PHP routes injected via window.FolioFormConfig (set inline in folio-form.blade.php)
document.addEventListener('DOMContentLoaded', function () {
    var config     = window.FolioFormConfig || {};
    var guestsUrl  = config.guestsUrl || '';
    var searchUrl  = config.searchUrl || '';

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    let currentStayId = null;
    const resultDiv    = document.getElementById('folio-sim-result');
    const folioActions = document.getElementById('folio-actions');
    const searchBtn    = document.getElementById('folio-search-btn');
    const guestSelect  = document.getElementById('folio-guest');
    const roomInput    = document.getElementById('folio-room');
    const guestLoading = document.getElementById('guest-loading');

    if (!searchBtn) return;

    // Cargar huéspedes activos al iniciar
    function loadActiveGuests() {
        guestLoading.style.display = 'block';
        fetch(guestsUrl, { headers: { 'Accept': 'application/json' } })
            .then(response => response.json())
            .then(guests => {
                guestLoading.style.display = 'none';

                if (guests.length === 0) {
                    guestSelect.innerHTML = '<option value="">No hay huéspedes alojados</option>';
                    return;
                }

                guestSelect.innerHTML = '<option value="">Seleccione un huésped...</option>';
                guests.forEach(guest => {
                    const option = document.createElement('option');
                    option.value = guest.id;
                    option.textContent = guest.name;
                    guestSelect.appendChild(option);
                });
            })
            .catch(error => {
                guestLoading.style.display = 'none';
                console.error('Error al cargar huéspedes:', error);
                showNotification('Error al cargar la lista de huéspedes', 'error');
            });
    }

    loadActiveGuests();

    // Sistema de notificaciones
    function showNotification(message, type = 'success') {
        const container = document.getElementById('notification-container');
        const notification = document.createElement('div');
        notification.className = `folio-notification ${type}`;

        const icons = {
            success: 'bi-check-circle-fill',
            error:   'bi-x-circle-fill',
            warning: 'bi-exclamation-triangle-fill'
        };

        notification.innerHTML = `
            <div class="notification-content">
                <i class="bi ${icons[type]} notification-icon"></i>
                <span class="notification-text">${message}</span>
                <button class="notification-close" onclick="this.closest('.folio-notification').remove()">
                    <i class="bi bi-x"></i>
                </button>
            </div>
        `;

        container.appendChild(notification);

        setTimeout(() => {
            notification.style.animation = 'slideOutRight 0.4s ease-out';
            setTimeout(() => notification.remove(), 400);
        }, 5000);
    }

    searchBtn.addEventListener('click', function () {
        const guestId    = guestSelect.value.trim();
        const roomNumber = roomInput.value.trim();

        if (!guestId && !roomNumber) {
            resultDiv.innerHTML = '<div class="alert alert-warning"><i class="bi bi-exclamation-triangle"></i> Debe seleccionar un huésped o ingresar número de habitación</div>';
            return;
        }

        resultDiv.innerHTML = '<div class="text-center"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">Buscando folio...</p></div>';

        fetch(searchUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ guest_id: guestId, room_number: roomNumber })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                currentStayId = data.stay.id;
                document.getElementById('current-stay-id').value = currentStayId;

                let html = '<div class="alert alert-success mb-4"><i class="bi bi-check-circle"></i> Folio encontrado</div>';
                html += '<div class="row g-3 mb-4">';
                html += '<div class="col-md-3"><strong>Estancia ID:</strong><br>' + data.stay.id + '</div>';
                html += '<div class="col-md-3"><strong>Huésped:</strong><br>' + (data.stay.guest ? data.stay.guest.first_name + ' ' + data.stay.guest.last_name : 'N/A') + '</div>';
                html += '<div class="col-md-3"><strong>Habitación:</strong><br>' + (data.stay.room ? data.stay.room.room_number : 'N/A') + '</div>';
                html += '<div class="col-md-3"><strong>Estado:</strong><br><span class="badge bg-info">' + data.stay.status + '</span></div>';
                html += '</div>';

                if (data.folio) {
                    html += '<div class="row g-3 mb-4">';
                    html += '<div class="col-md-3"><strong>Número Folio:</strong><br><span class="text-primary">' + data.folio.number + '</span></div>';
                    html += '<div class="col-md-3"><strong>Estado:</strong><br><span class="badge bg-warning">' + data.folio.status + '</span></div>';
                    html += '<div class="col-md-3"><strong>Moneda:</strong><br>' + data.folio.currency + '</div>';
                    html += '<div class="col-md-3"><strong>Balance:</strong><br><span class="text-danger fw-bold">$' + parseFloat(data.folio.balance).toLocaleString('es-CO', {minimumFractionDigits: 2}) + '</span></div>';
                    html += '</div>';

                    // Cargos
                    html += '<div class="mb-4"><h5 class="fw-bold">Cargos Registrados</h5>';
                    if (data.charges && data.charges.length > 0) {
                        html += '<table class="table table-sm table-striped"><thead><tr><th>Fecha</th><th>Tipo</th><th>Descripción</th><th>Monto</th><th>Impuesto</th><th>Total</th></tr></thead><tbody>';
                        data.charges.forEach(charge => {
                            let total = parseFloat(charge.amount) + parseFloat(charge.tax || 0);
                            html += '<tr>';
                            html += '<td>' + (charge.posted_at || charge.created_at).substring(0, 10) + '</td>';
                            html += '<td>' + charge.source + '</td>';
                            html += '<td>' + charge.description + '</td>';
                            html += '<td>$' + parseFloat(charge.amount).toLocaleString('es-CO', {minimumFractionDigits: 2}) + '</td>';
                            html += '<td>$' + parseFloat(charge.tax || 0).toLocaleString('es-CO', {minimumFractionDigits: 2}) + '</td>';
                            html += '<td class="fw-bold">$' + total.toLocaleString('es-CO', {minimumFractionDigits: 2}) + '</td>';
                            html += '</tr>';
                        });
                        html += '</tbody></table>';
                    } else {
                        html += '<p class="text-muted"><i class="bi bi-info-circle"></i> Sin cargos registrados</p>';
                    }
                    html += '</div>';

                    // Pagos
                    html += '<div class="mb-4"><h5 class="fw-bold">Pagos Registrados</h5>';
                    if (data.payments && data.payments.length > 0) {
                        html += '<table class="table table-sm table-striped"><thead><tr><th>Fecha</th><th>Método</th><th>Monto</th><th>Referencia</th></tr></thead><tbody>';
                        data.payments.forEach(payment => {
                            html += '<tr>';
                            html += '<td>' + (payment.received_at || payment.created_at).substring(0, 10) + '</td>';
                            html += '<td>' + payment.method + '</td>';
                            html += '<td class="fw-bold text-success">$' + parseFloat(payment.amount).toLocaleString('es-CO', {minimumFractionDigits: 2}) + '</td>';
                            html += '<td>' + (payment.external_ref || '-') + '</td>';
                            html += '</tr>';
                        });
                        html += '</tbody></table>';
                    } else {
                        html += '<p class="text-muted"><i class="bi bi-info-circle"></i> Sin pagos registrados</p>';
                    }
                    html += '</div>';
                }

                resultDiv.innerHTML = html;
                folioActions.style.display = 'block';
            } else {
                resultDiv.innerHTML = '<div class="alert alert-danger"><i class="bi bi-x-circle"></i> ' + data.message + '</div>';
                folioActions.style.display = 'none';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            resultDiv.innerHTML = '<div class="alert alert-danger"><i class="bi bi-exclamation-triangle"></i> Error al buscar folio</div>';
        });
    });

    // Agregar cargo
    document.getElementById('add-charge-form').addEventListener('submit', function (e) {
        e.preventDefault();

        if (!currentStayId) {
            showNotification('Primero debe buscar un folio', 'warning');
            return;
        }

        const formData = {
            source:      document.getElementById('charge-source').value,
            description: document.getElementById('charge-desc').value,
            amount:      document.getElementById('charge-amount').value,
            tax:         document.getElementById('charge-tax').value || 0
        };

        fetch(`/reception/stay/${currentStayId}/charges`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify(formData)
        })
        .then(response => {
            if (!response.ok) return response.json().then(err => Promise.reject(err));
            return response.json();
        })
        .then(data => {
            if (data.success) {
                showNotification(data.message || 'Cargo agregado exitosamente', 'success');
                document.getElementById('add-charge-form').reset();
                searchBtn.click();
            } else {
                showNotification(data.message || 'Error al agregar el cargo', 'error');
            }
        })
        .catch(error => {
            showNotification(error.message || 'Error al agregar el cargo', 'error');
            console.error('Error:', error);
        });
    });

    // Agregar pago
    document.getElementById('add-payment-form').addEventListener('submit', function (e) {
        e.preventDefault();

        if (!currentStayId) {
            showNotification('Primero debe buscar un folio', 'warning');
            return;
        }

        const formData = {
            method:       document.getElementById('pay-method').value,
            amount:       document.getElementById('pay-amount').value,
            currency:     'COP',
            external_ref: document.getElementById('pay-reference').value || null
        };

        fetch(`/reception/stay/${currentStayId}/payments`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify(formData)
        })
        .then(response => {
            if (!response.ok) return response.json().then(err => Promise.reject(err));
            return response.json();
        })
        .then(data => {
            if (data.success) {
                showNotification(data.message || 'Pago registrado exitosamente', 'success');
                document.getElementById('add-payment-form').reset();
                searchBtn.click();
            } else {
                showNotification(data.message || 'Error al registrar el pago', 'error');
            }
        })
        .catch(error => {
            showNotification(error.message || 'Error al registrar el pago', 'error');
            console.error('Error:', error);
        });
    });
});
