<?php $__env->startSection('content'); ?>
<?php
    $adminView = true;
    $sidebarView = 'admin.sidebar'; // Usa el sidebar simple con 3 botones
?>
<style>
    .admin-dashboard {
        background: #f5f5f5;
        min-height: 100vh;
        padding: 24px 16px; /* más compacto para 100% zoom en portátil */
    }

    .dashboard-header {
        text-align: center;
        color: #333;
        margin-bottom: 32px; /* antes 50px */
        animation: fadeInDown 0.8s ease;
    }

    .dashboard-header h1 {
        font-size: 2.4rem; /* antes 3rem */
        font-weight: 800;
        margin-bottom: 6px;
        color: #2c3e50;
    }

    .dashboard-header p {
        font-size: 1.05rem; /* antes 1.3rem */
        color: #666;
    }

    .stats-container {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 18px; /* antes 25px */
        margin-bottom: 32px; /* antes 50px */
        max-width: 1400px;
        margin-left: auto;
        margin-right: auto;
    }

    .stat-box {
        background: white;
        border-radius: 14px;
        padding: 22px; /* antes 30px */
        box-shadow: 0 8px 22px rgba(0,0,0,0.16);
        transition: transform 0.3s, box-shadow 0.3s;
        animation: fadeInUp 0.8s ease;
    }

    .stat-box:hover {
        transform: translateY(-6px);
        box-shadow: 0 14px 32px rgba(0,0,0,0.24);
    }

    .stat-box .icon-wrapper {
        width: 56px; /* antes 70px */
        height: 56px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 14px;
        font-size: 1.6rem;
    }

    .stat-box.reservas .icon-wrapper {
        background: linear-gradient(135deg, #FF9800 0%, #FF6F00 100%);
        color: white;
    }

    .stat-box.minibar .icon-wrapper {
        background: linear-gradient(135deg, #4CAF50 0%, #2E7D32 100%);
        color: white;
    }

    .stat-box.recepcion .icon-wrapper {
        background: linear-gradient(135deg, #2196F3 0%, #1565C0 100%);
        color: white;
    }

    .stat-box h3 {
        font-size: 2.2rem; /* antes 2.8rem */
        font-weight: 800;
        color: #333;
        margin-bottom: 6px;
    }

    .stat-box p {
        color: #666;
        font-size: 0.98rem;
        margin: 0;
        font-weight: 500;
    }

    .stat-box .subtitle {
        color: #999;
        font-size: 0.86rem;
        margin-top: 4px;
    }

    .panels-container {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px; /* antes 30px */
        max-width: 1400px;
        margin: 0 auto;
    }

    .panel-card {
        background: white;
        border-radius: 18px;
        padding: 24px 18px; /* antes 50px 40px - más compacto */
        text-align: center;
        box-shadow: 0 12px 34px rgba(0,0,0,0.2);
        transition: transform 0.4s, box-shadow 0.4s;
        position: relative;
        overflow: hidden;
        animation: fadeInUp 1s ease;
        text-decoration: none;
        display: block;
    }

    .panel-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
        transition: left 0.5s;
    }

    .panel-card:hover::before {
        left: 100%;
    }

    .panel-card:hover {
        transform: translateY(-10px) scale(1.01);
        box-shadow: 0 18px 44px rgba(0,0,0,0.28);
        text-decoration: none;
    }

    .panel-card .panel-icon {
        width: 70px; /* antes 88px */
        height: 70px;
        margin: 0 auto 14px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.4rem; /* antes 3rem */
        color: white;
        box-shadow: 0 8px 22px rgba(0,0,0,0.18);
    }

    .panel-card.inicio .panel-icon {
        background: linear-gradient(135deg, #2196F3 0%, #1565C0 100%);
    }

    .panel-card.reservas .panel-icon {
        background: linear-gradient(135deg, #FF9800 0%, #E65100 100%);
    }

    .panel-card.minibar .panel-icon {
        background: linear-gradient(135deg, #4CAF50 0%, #1B5E20 100%);
    }

    .panel-card.recepcion .panel-icon {
        background: linear-gradient(135deg, #2196F3 0%, #0D47A1 100%);
    }

    .panel-card h2 {
        font-size: 1.25rem; /* antes 1.5rem */
        font-weight: 800;
        color: #333;
        margin-bottom: 8px;
    }

    .panel-card p {
        color: #666;
        font-size: 0.9rem; /* antes 0.98rem */
        line-height: 1.45;
        margin-bottom: 0;
    }

    .panel-card .arrow {
        margin-top: 14px;
        font-size: 1.6rem;
        color: #999;
        transition: transform 0.3s;
    }

    .panel-card:hover .arrow {
        transform: translateX(10px);
    }

    @keyframes fadeInDown {
        from {
            opacity: 0;
            transform: translateY(-30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .section-title {
        text-align: center;
        color: #2c3e50;
        font-size: 1.8rem; /* antes 2.2rem */
        font-weight: 700;
        margin: 40px auto 24px; /* antes 60px auto 40px */
        padding: 0 16px;
        display: block !important;   /* override global inline-block */
        width: 100%;
        max-width: 1400px;          /* align with content width */
    }

    /* Escala suave para pantallas muy anchas (>=1600px) */
    @media (min-width: 1600px) {
        .dashboard-header h1 { font-size: 2.8rem; }
        .dashboard-header p { font-size: 1.15rem; }
        .stat-box { padding: 26px; }
        .stat-box .icon-wrapper { width: 64px; height: 64px; font-size: 1.8rem; }
        .stat-box h3 { font-size: 2.4rem; }
        .panels-container { gap: 26px; }
        .panel-card { padding: 36px 28px; }
        .panel-card .panel-icon { width: 96px; height: 96px; font-size: 3.2rem; }
        .panel-card h2 { font-size: 1.6rem; }
    }
    /* Override global theme lines for .section-title on admin page */
    .admin-dashboard .section-title::before,
    .admin-dashboard .section-title::after {
        display: none !important;
        content: none !important;
    }

    /* =============================
       Responsive tweaks (tablet/móvil)
       ============================= */
    @media (max-width: 992px) {
        .admin-dashboard { padding: 30px 16px; }
        .dashboard-header h1 { font-size: 2.4rem; }
        .dashboard-header p { font-size: 1.1rem; }
        .stats-container { grid-template-columns: repeat(2, 1fr); gap: 18px; }
        .panels-container { grid-template-columns: repeat(2, 1fr); gap: 18px; }
        .panel-card { padding: 28px 20px; }
        .panel-card .panel-icon { width: 80px; height: 80px; font-size: 2.6rem; }
        .panel-card h2 { font-size: 1.35rem; }
    }

    @media (max-width: 576px) {
        .admin-dashboard { padding: 20px 10px; }
        .dashboard-header h1 { font-size: 1.9rem; }
        .dashboard-header p { font-size: 1rem; }
        .section-title { font-size: 1.5rem; margin: 30px auto 20px; }
        .stats-container { grid-template-columns: 1fr; gap: 14px; }
        .stat-box { padding: 18px; }
        .stat-box h3 { font-size: 1.8rem; }
        .panels-container { grid-template-columns: 1fr; gap: 16px; }
        .panel-card { padding: 24px 18px; }
        .panel-card .panel-icon { width: 72px; height: 72px; font-size: 2.2rem; margin-bottom: 16px; }
        .panel-card h2 { font-size: 1.4rem; }
    }
</style>

<div class="admin-dashboard">
    <div class="dashboard-header">
        <h1>Panel de Administración</h1>
        <p>Hotel Oasis de la Colina</p>
    </div>

    <!-- Estadísticas Generales -->
    <div class="stats-container">
        <div class="stat-box reservas">
            <div class="icon-wrapper">
                <i class="bi bi-door-open-fill"></i>
            </div>
            <h3><?php echo e($totalRooms); ?></h3>
            <p>Total Habitaciones</p>
            <p class="subtitle">Disponibles en el hotel</p>
        </div>

        <div class="stat-box reservas">
            <div class="icon-wrapper">
                <i class="bi bi-calendar-check-fill"></i>
            </div>
            <h3><?php echo e($reservedRoom); ?></h3>
            <p>Reservas Activas</p>
            <p class="subtitle">En proceso actualmente</p>
        </div>

        <div class="stat-box minibar">
            <div class="icon-wrapper">
                <i class="bi bi-basket-fill"></i>
            </div>
            <h3><?php echo e($totalProductos); ?></h3>
            <p>Productos Minibar</p>
            <p class="subtitle">En el catálogo</p>
        </div>

        <div class="stat-box minibar">
            <div class="icon-wrapper">
                <i class="bi bi-receipt-cutoff"></i>
            </div>
            <h3><?php echo e($totalCompras); ?></h3>
            <p>Ventas Realizadas</p>
            <p class="subtitle">Total de compras</p>
        </div>

        <div class="stat-box recepcion">
            <div class="icon-wrapper">
                <i class="bi bi-door-open"></i>
            </div>
            <h3>0</h3>
            <p>Check-ins Hoy</p>
            <p class="subtitle">Llegadas registradas</p>
        </div>

        <div class="stat-box recepcion">
            <div class="icon-wrapper">
                <i class="bi bi-people-fill"></i>
            </div>
            <h3>0</h3>
            <p>Huéspedes en Casa</p>
            <p class="subtitle">Estancias activas</p>
        </div>
    </div>

    <h2 class="section-title">Acceso a Paneles de Gestión</h2>

    <!-- Paneles de Gestión -->
    <div class="panels-container">
        <a href="<?php echo e(route('home')); ?>" class="panel-card inicio">
            <div class="panel-icon">
                <i class="bi bi-house-door-fill"></i>
            </div>
            <h2>Inicio</h2>
            <p>Ir a la página principal del Hotel Oasis de la Colina</p>
            <div class="arrow">→</div>
        </a>

        <a href="<?php echo e(route('admin.habitaciones.dashboard')); ?>" class="panel-card reservas">
            <div class="panel-icon">
                <i class="bi bi-calendar-event-fill"></i>
            </div>
            <h2>Panel de Reservas</h2>
            <p>Gestionar habitaciones, tipos de habitación y reservas de huéspedes</p>
            <div class="arrow">→</div>
        </a>

        <a href="<?php echo e(route('admin.minibar.dashboard')); ?>" class="panel-card minibar">
            <div class="panel-icon">
                <i class="bi bi-shop"></i>
            </div>
            <h2>Panel de Minibar</h2>
            <p>Administrar productos, bebidas, tipos y ventas del minibar</p>
            <div class="arrow">→</div>
        </a>

        <a href="<?php echo e(route('reception.dashboard')); ?>" class="panel-card recepcion">
            <div class="panel-icon">
                <i class="bi bi-reception-4"></i>
            </div>
            <h2>Panel de Recepción</h2>
            <p>Gestionar check-in, folios, cargos y check-out de huéspedes</p>
            <div class="arrow">→</div>
        </a>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\hotel-piloto-sam\resources\views/admin/index.blade.php ENDPATH**/ ?>