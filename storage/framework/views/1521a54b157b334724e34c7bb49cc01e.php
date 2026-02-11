<?php $__env->startSection('content'); ?>
<?php
    $adminView = true;
    $sidebarView = 'admin.sidebar';
?>

<style>
    .reception-dashboard {
        padding: 40px 60px;
        max-width: 100%;
        width: 100%;
    }

    .page-header {
        margin-bottom: 40px;
        border-bottom: 3px solid #2196F3;
        padding-bottom: 20px;
    }

    .page-header h1 {
        color: #2196F3;
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 10px;
    }

    .page-header p {
        color: #666;
        font-size: 1.1rem;
    }

    .stats-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 25px;
        margin-bottom: 40px;
    }

    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 30px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        border-left: 5px solid #2196F3;
        transition: transform 0.3s, box-shadow 0.3s;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    }

    .stat-card .icon {
        font-size: 3rem;
        color: #2196F3;
        margin-bottom: 15px;
    }

    .stat-card h3 {
        font-size: 3rem;
        font-weight: 800;
        color: #333;
        margin-bottom: 10px;
    }

    .stat-card p {
        color: #666;
        font-size: 1.1rem;
        margin: 0;
    }

    .action-buttons {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 20px;
        margin-top: 30px;
    }

    .action-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px 30px;
        background: linear-gradient(135deg, #2196F3 0%, #1565C0 100%);
        color: white;
        border-radius: 10px;
        text-decoration: none;
        font-size: 1.2rem;
        font-weight: 600;
        transition: transform 0.3s, box-shadow 0.3s;
        box-shadow: 0 4px 12px rgba(33, 150, 243, 0.3);
    }

    .action-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 20px rgba(33, 150, 243, 0.4);
        color: white;
        text-decoration: none;
    }

    .action-btn i {
        margin-right: 10px;
        font-size: 1.5rem;
    }

    .action-btn.secondary {
        background: linear-gradient(135deg, #64B5F6 0%, #1976D2 100%);
        box-shadow: 0 4px 12px rgba(100, 181, 246, 0.3);
    }

    .action-btn.secondary:hover {
        box-shadow: 0 6px 20px rgba(100, 181, 246, 0.4);
    }

    .action-btn.tertiary {
        background: linear-gradient(135deg, #1565C0 0%, #0D47A1 100%);
        box-shadow: 0 4px 12px rgba(21, 101, 192, 0.3);
    }

    .action-btn.tertiary:hover {
        box-shadow: 0 6px 20px rgba(21, 101, 192, 0.4);
    }
</style>

<div class="reception-dashboard">

    <div id="section-dashboard">
        <div class="page-header">
            <h1><i class="bi bi-reception-4"></i> Recepción — Tablero</h1>
            <p>Resumen rápido de llegadas, salidas y huéspedes en casa</p>
        </div>

        <div class="stats-row">
            <div class="stat-card">
                <div class="icon">
                    <i class="bi bi-calendar-check"></i>
                </div>
                <h3><?php echo e($arrivals->count() ?? 0); ?></h3>
                <p>Llegadas de hoy</p>
            </div>

            <div class="stat-card">
                <div class="icon">
                    <i class="bi bi-box-arrow-right"></i>
                </div>
                <h3><?php echo e($departures->count() ?? 0); ?></h3>
                <p>Salidas de hoy</p>
            </div>

            <div class="stat-card">
                <div class="icon">
                    <i class="bi bi-people"></i>
                </div>
                <h3><?php echo e($inHouse->count() ?? 0); ?></h3>
                <p>Huéspedes en casa</p>
            </div>
        </div>

        <h2 style="color: #333; margin-bottom: 20px; font-weight: 700;">
            <i class="bi bi-gear"></i> Gestión de Recepción
        </h2>

        <div class="action-buttons">
            <a href="#checkin" class="action-btn">
                <i class="bi bi-door-open"></i>
                Ir a Check-in
            </a>

            <a href="#folio" class="action-btn secondary">
                <i class="bi bi-receipt"></i>
                Ver Folios
            </a>

            <a href="#checkout" class="action-btn tertiary">
                <i class="bi bi-box-arrow-in-right"></i>
                Procesar Check-out
            </a>
        </div>
    </div>

    <div id="section-checkin" style="display:none;">
        <?php echo $__env->make('reception.partials.checkin-form', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </div>

    <div id="section-folio" style="display:none;">
        <?php echo $__env->make('reception.partials.folio-form', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </div>

    <div id="section-checkout" style="display:none;">
        <?php echo $__env->make('reception.partials.checkout-form', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        function showSection(id) {
            ['section-dashboard','section-checkin','section-folio','section-checkout'].forEach(function(s){
                var el = document.getElementById(s);
                if (!el) return;
                el.style.display = (s === id) ? '' : 'none';
            });
        }

        // Handle sidebar links with hashes
        document.querySelectorAll('a[href$="#checkin"], a[href$="#folio"], a[href$="#checkout"], a[href$="<?php echo e(route('reception.dashboard')); ?>"]').forEach(function(a){
            a.addEventListener('click', function(e){
                // If the link goes to the reception dashboard (or an anchor) prevent full navigation
                var href = a.getAttribute('href') || '';
                if (href.indexOf('#checkin') !== -1) {
                    e.preventDefault();
                    history.replaceState(null,'', '#checkin');
                    showSection('section-checkin');
                } else if (href.indexOf('#folio') !== -1) {
                    e.preventDefault();
                    history.replaceState(null,'', '#folio');
                    showSection('section-folio');
                } else if (href.indexOf('#checkout') !== -1) {
                    e.preventDefault();
                    history.replaceState(null,'', '#checkout');
                    showSection('section-checkout');
                } else if (href === '<?php echo e(route('reception.dashboard')); ?>' || href.endsWith('/reception/dashboard')) {
                    e.preventDefault();
                    history.replaceState(null,'', location.pathname);
                    showSection('section-dashboard');
                }
            });
        });

        // Always show dashboard on initial load
        // Check for hash in URL on initial load
        var hash = window.location.hash;
        if (hash === '#checkin') {
            showSection('section-checkin');
        } else if (hash === '#folio') {
            showSection('section-folio');
        } else if (hash === '#checkout') {
            showSection('section-checkout');
        } else {
            showSection('section-dashboard');
        }

        // Attach simulated handlers for folio and checkout forms (check-in now has its own script)
        var folioSearch = document.getElementById('folio-search-btn');
        if (folioSearch) {
            folioSearch.addEventListener('click', function(){
                var stay = document.getElementById('folio-stay').value || '';
                var room = document.getElementById('folio-room').value || '';
                var out = document.getElementById('folio-sim-result');
                if (!stay.trim() && !room.trim()) { out.innerHTML = '<div class="text-muted">Ingrese ID de estancia o habitación.</div>'; return; }
                out.innerHTML = '<div class="p-3 border rounded">Folio simulado para estancia <strong>'+escapeHtml(stay||room)+'</strong>.</div>';
            });
        }

        var chargeBtn = document.getElementById('charge-btn');
        if (chargeBtn) {
            chargeBtn.addEventListener('click', function(){
                var desc = document.getElementById('charge-desc').value || '';
                var amt = document.getElementById('charge-amount').value || '';
                alert('Cargo simulado: '+desc+' — '+amt);
            });
        }

        var payBtn = document.getElementById('pay-btn');
        if (payBtn) {
            payBtn.addEventListener('click', function(){
                var method = document.getElementById('pay-method').value || '';
                var amt = document.getElementById('pay-amount').value || '';
                alert('Pago simulado: '+method+' — '+amt);
            });
        }

        var coBtn = document.getElementById('checkout-btn');
        if (coBtn) {
            coBtn.addEventListener('click', function(){
                var stay = document.getElementById('co-stay').value || '';
                if (!stay.trim()) { document.getElementById('checkout-result').innerHTML = '<div class="text-muted">Ingrese ID de estancia.</div>'; return; }
                document.getElementById('checkout-result').innerHTML = '<div class="p-3 border rounded">Check-out simulado para estancia <strong>'+escapeHtml(stay)+'</strong>.</div>';
            });
        }

        function escapeHtml(text) { return String(text).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }
    });
    </script>

<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\hotel-piloto-sam\resources\views/reception/dashboard.blade.php ENDPATH**/ ?>