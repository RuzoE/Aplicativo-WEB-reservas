// Reception dashboard: section switcher + simulated stubs
// PHP data injected via window.ReceptionConfig (set inline in reception/dashboard.blade.php)
document.addEventListener('DOMContentLoaded', function () {
    var configEl = document.getElementById('reception-dashboard-config');
    var dashboardUrl = configEl ? (configEl.dataset.dashboardUrl || '') : '';
    var showCheckinNow = configEl ? (configEl.dataset.showCheckinSection === '1') : false;

    function showSection(id) {
        ['section-dashboard', 'section-checkin', 'section-userlink', 'section-folio', 'section-checkout'].forEach(function (s) {
            var el = document.getElementById(s);
            if (!el) return;
            el.style.display = (s === id) ? '' : 'none';
        });
    }

    // Build selector including the dashboard URL if we have it
    var baseSelector = 'a[href$="#checkin"], a[href$="#userlink"], a[href$="#folio"], a[href$="#checkout"]';
    var selectorWithDash = dashboardUrl
        ? baseSelector + ', a[href="' + dashboardUrl + '"]'
        : baseSelector;

    document.querySelectorAll(selectorWithDash).forEach(function (a) {
        a.addEventListener('click', function (e) {
            var href = a.getAttribute('href') || '';
            if (href.indexOf('#checkin') !== -1) {
                e.preventDefault();
                history.replaceState(null, '', '#checkin');
                showSection('section-checkin');
            } else if (href.indexOf('#userlink') !== -1) {
                e.preventDefault();
                history.replaceState(null, '', '#userlink');
                showSection('section-userlink');
            } else if (href.indexOf('#folio') !== -1) {
                e.preventDefault();
                history.replaceState(null, '', '#folio');
                showSection('section-folio');
            } else if (href.indexOf('#checkout') !== -1) {
                e.preventDefault();
                history.replaceState(null, '', '#checkout');
                showSection('section-checkout');
            } else if (href === dashboardUrl || href.endsWith('/reception/dashboard')) {
                e.preventDefault();
                history.replaceState(null, '', location.pathname);
                showSection('section-dashboard');
            }
        });
    });

    // Initial section selection (server-side hint or URL hash)
    if (showCheckinNow) {
        showSection('section-checkin');
    } else {
        var hash = window.location.hash;
        if (hash === '#checkin') {
            showSection('section-checkin');
        } else if (hash === '#userlink') {
            showSection('section-userlink');
        } else if (hash === '#folio') {
            showSection('section-folio');
        } else if (hash === '#checkout') {
            showSection('section-checkout');
        } else {
            showSection('section-dashboard');
        }
    }

    // Simulated stub handlers (kept for backwards compatibility)
    var folioSearch = document.getElementById('folio-search-btn');
    if (folioSearch) {
        folioSearch.addEventListener('click', function () {
            var stay = document.getElementById('folio-stay') ? document.getElementById('folio-stay').value : '';
            var room = document.getElementById('folio-room') ? document.getElementById('folio-room').value : '';
            var out  = document.getElementById('folio-sim-result');
            if (!out) return;
            if (!stay.trim() && !room.trim()) {
                out.innerHTML = '<div class="text-muted">Ingrese ID de estancia o habitación.</div>';
                return;
            }
            out.innerHTML = '<div class="p-3 border rounded">Folio simulado para estancia <strong>' + escapeHtml(stay || room) + '</strong>.</div>';
        });
    }

    var chargeBtn = document.getElementById('charge-btn');
    if (chargeBtn) {
        chargeBtn.addEventListener('click', function () {
            var desc = document.getElementById('charge-desc') ? document.getElementById('charge-desc').value : '';
            var amt  = document.getElementById('charge-amount') ? document.getElementById('charge-amount').value : '';
            if (!desc.trim() || !amt.trim()) return;
        });
    }

    var payBtn = document.getElementById('pay-btn');
    if (payBtn) {
        payBtn.addEventListener('click', function () {
            var method = document.getElementById('pay-method') ? document.getElementById('pay-method').value : '';
            var amt    = document.getElementById('pay-amount') ? document.getElementById('pay-amount').value : '';
            if (!method.trim() || !amt.trim()) return;
        });
    }

    var coBtn = document.getElementById('checkout-btn');
    if (coBtn) {
        coBtn.addEventListener('click', function () {
            var stay   = document.getElementById('co-stay') ? document.getElementById('co-stay').value : '';
            var result = document.getElementById('checkout-result');
            if (!result) return;
            if (!stay.trim()) {
                result.innerHTML = '<div class="text-muted">Ingrese ID de estancia.</div>';
                return;
            }
            result.innerHTML = '<div class="p-3 border rounded">Check-out simulado para estancia <strong>' + escapeHtml(stay) + '</strong>.</div>';
        });
    }

    function escapeHtml(text) {
        return String(text)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');
    }
});
