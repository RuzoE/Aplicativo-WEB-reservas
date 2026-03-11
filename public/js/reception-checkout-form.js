// Reception checkout form: search stay, register payment, process check-out
// PHP routes injected via window.CheckoutFormConfig (set inline in checkout-form.blade.php)
document.addEventListener('DOMContentLoaded', function () {
    var config      = window.CheckoutFormConfig || {};
    var searchUrl   = config.folioSearchUrl || '';
    var dashboardUrl = config.dashboardUrl  || '/reception/dashboard';

    const searchBtn        = document.getElementById('co-btn-search');
    const searchInput      = document.getElementById('co-search-input');
    const searchError      = document.getElementById('co-search-error');
    const detailsContainer = document.getElementById('co-details-container');
    const paymentSection   = document.getElementById('co-payment-section');
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
        document.getElementById('co-pay-msg').innerHTML = '';

        document.getElementById('co-guest-name').innerText   = data.stay.guest.first_name + ' ' + data.stay.guest.last_name;
        document.getElementById('co-guest-doc').innerText    = data.stay.guest.document_type + ' ' + data.stay.guest.document_number;
        document.getElementById('co-guest-room').innerText   = data.stay.room ? data.stay.room.total_room : 'N/A';

        let dateObj = new Date(data.stay.arrival_at);
        document.getElementById('co-guest-checkin').innerText = isNaN(dateObj) ? 'N/A' : dateObj.toLocaleDateString();

        let totalCharges  = 0;
        let totalPayments = 0;

        if (data.charges)  totalCharges  = data.charges.reduce((sum, item)  => sum + parseFloat(item.amount), 0);
        if (data.payments) totalPayments = data.payments.reduce((sum, item) => sum + parseFloat(item.amount), 0);

        currentBalance = totalCharges - totalPayments;
        if (Math.abs(currentBalance) < 0.01) currentBalance = 0;

        document.getElementById('co-total-charges').innerText  = '$' + totalCharges.toFixed(2);
        document.getElementById('co-total-payments').innerText = '$' + totalPayments.toFixed(2);

        const balanceEl = document.getElementById('co-balance');
        balanceEl.innerText = '$' + currentBalance.toFixed(2);

        if (currentBalance > 0) {
            balanceEl.classList.remove('text-success');
            balanceEl.classList.add('text-danger');
            paymentSection.style.display = 'block';
            document.getElementById('co-pay-amount').value = currentBalance.toFixed(2);
            processBtn.disabled = true;
            processBtn.style.opacity = '0.5';
        } else {
            balanceEl.classList.remove('text-danger');
            balanceEl.classList.add('text-success');
            paymentSection.style.display = 'none';
            processBtn.disabled = false;
            processBtn.style.opacity = '1';
        }

        detailsContainer.style.display = 'block';
    }

    function showError(message) {
        searchError.innerText = message;
        searchError.style.display = 'block';
    }

    // Registrar Pago
    document.getElementById('co-btn-pay').addEventListener('click', function () {
        if (!currentStayId) return;

        const amount = parseFloat(document.getElementById('co-pay-amount').value);
        if (isNaN(amount) || amount <= 0) {
            document.getElementById('co-pay-msg').innerHTML = '<span class="text-danger">Ingrese un monto válido</span>';
            return;
        }

        const method = document.getElementById('co-pay-method').value;
        const btn = this;
        btn.disabled = true;
        btn.innerHTML = 'Procesando...';

        fetch(`/reception/stay/${currentStayId}/payments`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ method: method, amount: amount, currency: 'USD' })
        })
        .then(response => response.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = 'Registrar Pago';

            if (data.success) {
                document.getElementById('co-pay-msg').innerHTML = '<span class="text-success"><i class="bi bi-check-circle"></i> Pago registrado correctamente</span>';
                setTimeout(() => performSearch(), 1000);
            } else {
                document.getElementById('co-pay-msg').innerHTML = `<span class="text-danger">${data.message || 'Error al procesar el pago'}</span>`;
            }
        })
        .catch(error => {
            btn.disabled = false;
            btn.innerHTML = 'Registrar Pago';
            document.getElementById('co-pay-msg').innerHTML = '<span class="text-danger">Error de conexión</span>';
            console.error(error);
        });
    });

    // Procesar Check-out
    processBtn.addEventListener('click', function () {
        if (!currentStayId || currentBalance > 0) return;

        if (!confirm('¿Está seguro de procesar el Check-out? Esto cerrará la estancia de forma permanente.')) {
            return;
        }

        processBtn.disabled = true;
        processBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Procesando Check-out...';

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
                processMsg.innerHTML = `<div class="alert alert-success mt-2"><i class="bi bi-check-circle-fill"></i> ${data.message} Redirigiendo...</div>`;
                setTimeout(() => { window.location.href = dashboardUrl; }, 1500);
            } else {
                processBtn.disabled = false;
                processBtn.innerHTML = '<i class="bi bi-check-circle"></i> Procesar Check-out';
                processMsg.innerHTML = `<div class="alert alert-danger mt-2"><i class="bi bi-exclamation-triangle-fill"></i> ${data.message || 'Error al procesar check-out'}</div>`;
            }
        })
        .catch(error => {
            processBtn.disabled = false;
            processBtn.innerHTML = '<i class="bi bi-check-circle"></i> Procesar Check-out';
            processMsg.innerHTML = '<div class="alert alert-danger mt-2">Error de red. Asegúrate de estar conectado e inténtalo de nuevo.</div>';
            console.error('Error:', error);
        });
    });
});
