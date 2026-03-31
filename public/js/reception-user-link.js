document.addEventListener('DOMContentLoaded', function () {
    const configEl = document.getElementById('user-link-section-config');
    const guestsUrl = configEl ? (configEl.dataset.guestsUrl || '') : '';
    const searchUrl = configEl ? (configEl.dataset.searchUrl || '') : '';
    const userSearchUrl = configEl ? (configEl.dataset.userSearchUrl || '') : '';
    const linkUrlPattern = configEl ? (configEl.dataset.linkUrl || '') : '';
    const csrfToken = configEl ? (configEl.dataset.csrfToken || '') : '';

    let currentStayId = null;

    const guestSelect = document.getElementById('link-stay-guest');
    const btnFindStay = document.getElementById('btn-find-stay-link');
    const stayResultDiv = document.getElementById('link-stay-result');
    const userActionsDiv = document.getElementById('link-user-actions');
    const userSearchInput = document.getElementById('link-user-search-input');
    const btnSearchUser = document.getElementById('btn-search-web-user');
    const userSearchResults = document.getElementById('link-user-search-results');

    if (!btnFindStay) return;

    // 1. Show Notifications (In-page)
    function showNotification(msg, type = 'success') {
        const container = document.getElementById('user-link-notification-container');
        const alertDiv = document.createElement('div');
        const icon = type === 'success' ? 'bi-check-circle' : 'bi-exclamation-circle';
        const color = type === 'success' ? 'success' : 'danger';

        alertDiv.className = `alert alert-${color} alert-dismissible fade show shadow-lg border-0 d-flex align-items-center`;
        alertDiv.innerHTML = `
            <i class="bi ${icon} me-2 fs-4"></i>
            <div>${msg}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        container.appendChild(alertDiv);
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alertDiv);
            bsAlert.close();
        }, 4000);
    }

    // 2. Load active guests
    fetch(guestsUrl, { headers: { 'Accept': 'application/json' } })
        .then(r => r.json())
        .then(guests => {
            guestSelect.innerHTML = '<option value="">Seleccione un huésped...</option>';
            if (guests.length > 0) {
                guests.forEach(g => {
                    const opt = document.createElement('option');
                    opt.value = g.id;
                    opt.textContent = `${g.name} ${g.room ? '(Hab: ' + g.room + ')' : ''}`;
                    guestSelect.appendChild(opt);
                });
            } else {
                guestSelect.innerHTML = '<option value="">No hay huéspedes en casa</option>';
            }
        });

    // 3. Find stay
    btnFindStay.onclick = function() {
        const guestId = guestSelect.value;
        if (!guestId) {
            showNotification('Seleccione un huésped de la lista', 'error');
            return;
        }

        stayResultDiv.innerHTML = '<div class="text-center p-3"><div class="spinner-border text-primary" role="status"></div></div>';
        userActionsDiv.style.display = 'none';

        fetch(searchUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            body: JSON.stringify({ guest_id: guestId })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                currentStayId = data.stay.id;
                let h = `<div class="p-3 border rounded shadow-sm bg-white">
                    <div class="row align-items-center">
                        <div class="col-md-5"><strong>Huésped:</strong><br>${data.stay.guest.first_name} ${data.stay.guest.last_name}</div>
                        <div class="col-md-3"><strong>Habitación:</strong><br><span class="badge bg-secondary fs-6">${data.stay.assigned_room_number || 'N/A'}</span></div>
                        <div class="col-md-4">
                            <strong>Cuenta Web Actual:</strong><br>
                            ${data.stay.user ? `<span class="text-success fw-bold"><i class="bi bi-person-check"></i> ${data.stay.user.email}</span>` : '<span class="text-muted">Ninguna vinculada</span>'}
                        </div>
                    </div>
                </div>`;
                stayResultDiv.innerHTML = h;
                userActionsDiv.style.display = 'block';
            } else {
                stayResultDiv.innerHTML = `<div class="alert alert-danger mb-0">${data.message}</div>`;
            }
        });
    };

    // 4. Search Users
    let pendingUserId = null;
    const confirmModal = new bootstrap.Modal(document.getElementById('confirmLinkModal'));

    async function performUserSearch() {
        const query = userSearchInput.value;
        if (query.trim().length < 3) {
            showNotification('Mínimo 3 caracteres para buscar', 'error');
            return;
        }

        try {
            const resp = await fetch(`${userSearchUrl}?query=${query}`);
            const users = await resp.json();
            userSearchResults.innerHTML = '';
            if (users.length === 0) {
                userSearchResults.innerHTML = '<div class="list-group-item text-muted">No se encontraron usuarios</div>';
            } else {
                users.forEach(u => {
                    const b = document.createElement('button');
                    b.className = 'list-group-item list-group-item-action d-flex justify-content-between align-items-center';
                    b.innerHTML = `<div><strong>${u.name} ${u.last_name || ''}</strong><br><small>${u.email}</small></div><span class="btn btn-sm btn-primary">Vincular</span>`;
                    b.onclick = () => {
                        pendingUserId = u.id;
                        document.getElementById('confirm-user-name').textContent = `${u.name} (${u.email})`;
                        confirmModal.show();
                    };
                    userSearchResults.appendChild(b);
                });
            }
            userSearchResults.style.display = 'block';
        } catch (e) {
            console.error(e);
            showNotification('Error técnico al buscar usuarios', 'error');
        }
    }

    // 5. Link User
    document.getElementById('btn-confirm-link-finalize').onclick = async function() {
        if (!pendingUserId || !currentStayId) return;
        confirmModal.hide();

        try {
            const finalUrl = linkUrlPattern.replace(':id', currentStayId);
            const resp = await fetch(finalUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ user_id: pendingUserId })
            });
            const data = await resp.json();
            if (data.success) {
                showNotification('¡Usuario vinculado exitosamente!', 'success');
                btnFindStay.click(); // Recargar datos
                userSearchResults.style.display = 'none';
                userSearchInput.value = '';
            } else {
                showNotification(data.message || 'Error al vincular', 'error');
            }
        } catch (e) {
            console.error(e);
            showNotification('Error al procesar la vinculación', 'error');
        }
    };

    btnSearchUser.onclick = performUserSearch;
    userSearchInput.onkeypress = (e) => { if (e.key === 'Enter') performUserSearch(); };

    // Close results dropdown on outside click
    document.addEventListener('click', (e) => {
        if (!userSearchResults.contains(e.target) && e.target !== userSearchInput && e.target !== btnSearchUser && !btnSearchUser.contains(e.target)) {
            userSearchResults.style.display = 'none';
        }
    });
});
