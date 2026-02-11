<?php $__env->startSection('content'); ?>
<?php
    $adminView = true;
    $sidebarView = 'admin.habitaciones-sidebar';
?>

<style>
    .reservas-dashboard {
        padding: 30px;
    }

    .page-header {
        margin-bottom: 40px;
        border-bottom: 3px solid #FF9800;
        padding-bottom: 20px;
    }

    .page-header h1 {
        color: #FF9800;
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
        border-left: 5px solid #FF9800;
        transition: transform 0.3s, box-shadow 0.3s;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    }

    .stat-card .icon {
        font-size: 3rem;
        color: #FF9800;
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
        background: linear-gradient(135deg, #FF9800 0%, #FF6F00 100%);
        color: white;
        border-radius: 10px;
        text-decoration: none;
        font-size: 1.2rem;
        font-weight: 600;
        transition: transform 0.3s, box-shadow 0.3s;
        box-shadow: 0 4px 12px rgba(255, 152, 0, 0.3);
    }

    .action-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 20px rgba(255, 152, 0, 0.4);
        color: white;
        text-decoration: none;
    }

    .action-btn i {
        margin-right: 10px;
        font-size: 1.5rem;
    }

    .action-btn.secondary {
        background: linear-gradient(135deg, #2196F3 0%, #1565C0 100%);
        box-shadow: 0 4px 12px rgba(33, 150, 243, 0.3);
    }

    .action-btn.secondary:hover {
        box-shadow: 0 6px 20px rgba(33, 150, 243, 0.4);
    }

    .action-btn.tertiary {
        background: linear-gradient(135deg, #4CAF50 0%, #2E7D32 100%);
        box-shadow: 0 4px 12px rgba(76, 175, 80, 0.3);
    }

    .action-btn.tertiary:hover {
        box-shadow: 0 6px 20px rgba(76, 175, 80, 0.4);
    }
</style>

<div class="reservas-dashboard">
    <div class="page-header">
        <h1><i class="bi bi-calendar-check"></i> Dashboard de Reservas</h1>
        <p>Gestión de habitaciones, tipos de habitación y reservas del hotel</p>
    </div>

    <div class="stats-row">
        <div class="stat-card">
            <div class="icon">
                <i class="bi bi-door-open"></i>
            </div>
            <h3><?php echo e($totalRooms); ?></h3>
            <p>Total de Habitaciones</p>
        </div>

        <div class="stat-card">
            <div class="icon">
                <i class="bi bi-calendar-check"></i>
            </div>
            <h3><?php echo e($reservedRoom); ?></h3>
            <p>Reservas Activas</p>
        </div>

        <div class="stat-card">
            <div class="icon">
                <i class="bi bi-door-closed"></i>
            </div>
            <h3><?php echo e($totalRooms - $reservedRoom); ?></h3>
            <p>Habitaciones Disponibles</p>
        </div>
    </div>

    <h2 style="color: #333; margin-bottom: 20px; font-weight: 700;">
        <i class="bi bi-gear"></i> Gestión de Reservas
    </h2>

    <div class="action-buttons">
        <a href="<?php echo e(route('admin.habitaciones.orders.index')); ?>" class="action-btn">
            <i class="bi bi-list-check"></i>
            Ver Todas las Reservas
        </a>

        <a href="<?php echo e(route('admin.habitaciones.roomtypes.index')); ?>" class="action-btn secondary">
            <i class="bi bi-grid-3x3"></i>
            Gestionar Tipos de Habitación
        </a>

        <a href="<?php echo e(route('admin.habitaciones.rooms.index')); ?>" class="action-btn tertiary">
            <i class="bi bi-house-door"></i>
            Gestionar Habitaciones
        </a>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\hotel-piloto-sam\resources\views/admin/habitaciones/dashboard.blade.php ENDPATH**/ ?>