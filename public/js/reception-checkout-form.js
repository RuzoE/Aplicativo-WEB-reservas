// Reception checkout form: search stay, register payment, process check-out
// PHP routes injected via window.CheckoutFormConfig (set inline in checkout-form.blade.php)
document.addEventListener('DOMContentLoaded', function () {
    var configEl = document.getElementById('checkout-section-config');
    var searchUrl = configEl ? (configEl.dataset.folioSearchUrl || '') : '';
    var dashboardUrl = configEl ? (configEl.dataset.dashboardUrl || '/reception/dashboard') : '/reception/dashboard';

    const searchBtn        = document.getElementById('co-btn-search');
    const searchInput      = document.getElementById('co-search-input');
    const searchError      = document.getElementById('co-search-error');
    const detailsContainer = document.getElementById('co-details-container');
    const processBtn       = document.getElementById('co-btn-process');
    const processMsg       = document.getElementById('co-process-msg');

    if (!searchBtn) return;

    let currentStayId = null;
    let currentBalance = 0;

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    searchBtn.addEventListener('click', performSearch);
    searchInput.addEventListener('keypress', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            performSearch();
        }
    });

    function performSearch() {
        const query = searchInput.value.trim();
        if (!query) {
            showError('Ingrese un término de búsqueda.');
            return;
        }

        searchBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Buscando...';
        searchBtn.disabled = true;
        searchError.style.display = 'none';
        detailsContainer.style.display = 'none';

        fetch(searchUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ query: query })
        })
        .then(response => response.json())
        .then(data => {
            searchBtn.innerHTML = 'Buscar';
            searchBtn.disabled = false;

            if (data.success && data.stay) {
                populateDetails(data);
            } else {
                showError(data.message || 'No se encontraron resultados.');
            }
        })
        .catch(error => {
            searchBtn.innerHTML = 'Buscar';
            searchBtn.disabled = false;
            showError('Error de conexión al buscar.');
            console.error('Error:', error);
        });
    }

    function populateDetails(data) {
        currentStayId = data.stay.id;
        document.getElementById('co-stay-id').value = currentStayId;

        processMsg.innerHTML = '';

        document.getElementById('co-guest-name').innerText   = data.stay.guest.first_name + ' ' + data.stay.guest.last_name;
        document.getElementById('co-guest-doc').innerText    = data.stay.guest.document_type + ' ' + data.stay.guest.document_number;
        document.getElementById('co-guest-room').innerText   = data.stay.assigned_room_number || (data.stay.room ? data.stay.room.total_room : 'N/A');

        let dateObj = new Date(data.stay.arrival_at);
        document.getElementById('co-guest-checkin').innerText = isNaN(dateObj) ? 'N/A' : dateObj.toLocaleDateString();

        let totalCharges  = 0;
        let totalPayments = 0;
        let reservationTotal = 0;

        if (data.billing) {
            reservationTotal = parseFloat(data.billing.reservation_total);
            totalCharges = parseFloat(data.billing.additional_charges) + reservationTotal;
            totalPayments = parseFloat(data.billing.total_paid);
            currentBalance = parseFloat(data.billing.balance);
        } else {
            if (data.charges)  totalCharges  = data.charges.reduce((sum, item)  => sum + parseFloat(item.amount), 0);
            if (data.payments) totalPayments = data.payments.reduce((sum, item) => sum + parseFloat(item.amount), 0);
            currentBalance = totalCharges - totalPayments;
        }

        if (Math.abs(currentBalance) < 0.01) currentBalance = 0;

        document.getElementById('co-total-charges').innerText  = '$' + totalCharges.toFixed(2);
        document.getElementById('co-total-payments').innerText = '$' + totalPayments.toFixed(2);

        const balanceEl = document.getElementById('co-balance');
        balanceEl.innerText = '$' + currentBalance.toFixed(2);

        if (currentBalance > 0) {
            balanceEl.classList.remove('text-success');
            balanceEl.classList.add('text-danger');
        } else {
            balanceEl.classList.remove('text-danger');
            balanceEl.classList.add('text-success');
        }

        // Always enable checkout button if a stay is loaded
        processBtn.disabled = false;
        processBtn.style.opacity = '1';

        detailsContainer.style.display = 'block';
    }

    function showError(message) {
        searchError.innerText = message;
        searchError.style.display = 'block';
    }

    function showNotification(msg, type = 'success') {
        const container = document.getElementById('checkout-notification-container');
        const alertDiv = document.createElement('div');
        const icon = type === 'success' ? 'bi-check-circle' : 'bi-exclamation-circle';
        const color = type === 'success' ? 'success' : 'danger';

        alertDiv.className = `alert alert-${color} d-flex align-items-center shadow-lg border-0 alert-dismissible fade show`;
        alertDiv.innerHTML = `
            <i class="bi ${icon} me-2 fs-4"></i>
            <div>${msg}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        container.appendChild(alertDiv);
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alertDiv);
            bsAlert.close();
        }, 5000);
    }

    // Procesar Check-out (Modal)
    const checkoutModal = new bootstrap.Modal(document.getElementById('confirmCheckoutModal'));

    processBtn.addEventListener('click', function () {
        if (!currentStayId) return;

        const guestName = document.getElementById('co-guest-name').innerText;
        document.getElementById('confirm-co-guest').innerText = guestName;
        checkoutModal.show();
    });

    document.getElementById('btn-confirm-checkout-finalize').onclick = function() {
        checkoutModal.hide();

        processBtn.disabled = true;
        processBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Procesando...';

        fetch(`/reception/check-out/${currentStayId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                let downloadBtnHtml = '';
                if (data.invoice_url) {
                    downloadBtnHtml = `<br><a href="${data.invoice_url}" class="btn btn-sm btn-outline-success mt-2 fw-bold" target="_blank"><i class="bi bi-download"></i> Descargar Comprobante PDF</a>`;

                    // Intentar descarga automática
                    const link = document.createElement('a');
                    link.href = data.invoice_url;
                    link.target = '_blank';
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                }

                processMsg.innerHTML = `<div class="alert alert-success mt-2"><i class="bi bi-check-circle-fill"></i> ${data.message} ${downloadBtnHtml}<br>Redirigiendo...</div>`;
                showNotification('Check-out completado con éxito');

                setTimeout(() => { window.location.href = dashboardUrl; }, 4000);
            } else {
                processBtn.disabled = false;
                processBtn.innerHTML = '<i class="bi bi-check-circle"></i> Procesar Check-out';
                processMsg.innerHTML = `<div class="alert alert-danger mt-2"><i class="bi bi-exclamation-triangle-fill"></i> ${data.message || 'Error al procesar check-out'}</div>`;
                showNotification(data.message || 'Error en el proceso', 'error');
            }
        })
        .catch(error => {
            processBtn.disabled = false;
            processBtn.innerHTML = '<i class="bi bi-check-circle"></i> Procesar Check-out';
            processMsg.innerHTML = '<div class="alert alert-danger mt-2">Error de red. Asegúrate de estar conectado e inténtalo de nuevo.</div>';
            console.error('Error:', error);
            showNotification('Error de red al procesar salida', 'error');
        });
    };
});
