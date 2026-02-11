<?php $__env->startSection('header'); ?>
    <?php echo $__env->make('layouts.header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <!-- Page Header Start -->
    <div class="container-fluid page-header mb-5 p-0" style="background-image: url(img/carousel-1.jpg);">
        <div class="container-fluid page-header-inner py-5">
            <div class="container text-center pb-5">
                <h1 class="display-3 text-white mb-3 animated slideInDown">Rooms</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb justify-content-center text-uppercase">
                        <li class="breadcrumb-item"><a href="#">Inicio</a></li>
                        <li class="breadcrumb-item"><a href="#">Paginas</a></li>
                        <li class="breadcrumb-item text-white active" aria-current="page">Reservas</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <!-- Page Header End -->
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="card">
        <div class="card-header">
            <h2>Mis Reservas</h2>
        </div>
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th scope="col">Habitación</th>
                    <th scope="col">Check in</th>
                    <th scope="col">Check out</th>
                    <th scope="col">Total</th>
                    <th scope="col">Reservado</th>
                </tr>
                </thead>
                <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e($order->room->roomtype->name); ?></td>
                        <td><?php echo e($order->check_in->setTimezone('America/Bogota')->format('d/m/Y h:i A')); ?></td>
                        <td><?php echo e($order->check_out->setTimezone('America/Bogota')->format('d/m/Y h:i A')); ?></td>
                        <td><?php 
	$__cop_args = [$order->room->price, $order->stayDays];
	$__cop_price = (float)($__cop_args[0] ?? 0);
	$__cop_days = (int)($__cop_args[1] ?? 1);
	$__cop_total = $__cop_price * max(1, $__cop_days);
	echo '$' . number_format($__cop_total, 0, ',', '.');
?></td>
                        <td><?php echo e($order->created_at->setTimezone('America/Bogota')->format('d/m/Y h:i A')); ?></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <p class="text-primary fw-bold">No tienes reservas aún.</p>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <!-- Newsletter -->
    <?php echo $__env->make('sections.newsletter', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('footer'); ?>
    <?php echo $__env->make('layouts.footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\hotel-piloto-sam\resources\views/pages/list-orders.blade.php ENDPATH**/ ?>