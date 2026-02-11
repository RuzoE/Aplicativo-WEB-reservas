<div class="container-fluid bg-dark px-0" style="position: sticky; top: 0; z-index: 1000;">
    <div class="row gx-0">
        <div class="col-lg-3 bg-dark d-none d-lg-block">
            <a href="<?php echo e(route('home')); ?>"
               class="navbar-brand w-100 h-100 m-0 p-0 d-flex align-items-center justify-content-center">
                <h1 class="m-0 text-primary text-uppercase">Hotel</h1>
            </a>
        </div>

        <div class="col-lg-9">
            <nav class="navbar navbar-expand-lg bg-dark navbar-dark p-3 p-lg-0">
                <a href="<?php echo e(route('home')); ?>" class="navbar-brand d-block d-lg-none">
                    <h1 class="m-0 text-primary text-uppercase">Hotel</h1>
                </a>

                <button type="button" class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse justify-content-between" id="navbarCollapse">

                    
                    <div class="navbar-nav mr-auto py-0">
                        <a class="nav-item nav-link <?php echo e(request()->routeIs('home') ? 'active' : ''); ?>"
                           href="<?php echo e(route('home')); ?>">Inicio</a>

                        <a class="nav-item nav-link <?php echo e(request()->routeIs('rooms.index') ? 'active' : ''); ?>"
                           href="<?php echo e(route('rooms.index')); ?>">Habitaciones</a>

                        <a class="nav-item nav-link <?php echo e(request()->routeIs('minibar.landing') ? 'active' : ''); ?>"
                           href="<?php echo e(route('minibar.landing')); ?>">Minibar</a>

                        <?php if(auth()->guard()->guest()): ?>
                            <a class="nav-item nav-link <?php echo e(request()->routeIs('login') ? 'active' : ''); ?>"
                               href="<?php echo e(route('login')); ?>">Iniciar Sesión</a>

                            <a class="nav-item nav-link <?php echo e(request()->routeIs('register') ? 'active' : ''); ?>"
                               href="<?php echo e(route('register')); ?>">Registrarse</a>
                        <?php else: ?>
                            
                            <div class="nav-item dropdown">
                                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                                    <?php echo e(Auth::user()->name); ?>

                                </a>
                                <div class="dropdown-menu rounded-0 m-0">
                                    <a href="<?php echo e(route('orders.index')); ?>" class="dropdown-item">Mis Reservas</a>
                                    <a href="<?php echo e(route('profile')); ?>" class="dropdown-item">Mi Perfil</a>
                                    <form method="post" action="<?php echo e(route('logout')); ?>">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="btn btn-link dropdown-item">Salir</button>
                                    </form>
                                </div>
                            </div>

                            
                            <?php if (\Illuminate\Support\Facades\Blade::check('hasanyrole', 'administrador|reservas|minibar|recepcion')): ?>
                                <?php if(Auth::user()->hasRole('administrador')): ?>
                                    <a href="<?php echo e(route('admin.index')); ?>"
                                       class="nav-item nav-link <?php echo e(request()->routeIs('admin.*') ? 'active' : ''); ?>">
                                        Panel
                                    </a>
                                <?php elseif(Auth::user()->hasRole('reservas')): ?>
                                    <a href="<?php echo e(route('admin.habitaciones.dashboard')); ?>"
                                       class="nav-item nav-link <?php echo e(request()->routeIs('admin.habitaciones.*') ? 'active' : ''); ?>">
                                        Panel
                                    </a>
                                <?php elseif(Auth::user()->hasRole('minibar')): ?>
                                    <a href="<?php echo e(route('admin.minibar.dashboard')); ?>"
                                       class="nav-item nav-link <?php echo e(request()->routeIs('admin.minibar.*') ? 'active' : ''); ?>">
                                        Panel
                                    </a>
                                <?php elseif(Auth::user()->hasRole('recepcion')): ?>
                                    <a href="<?php echo e(route('reception.dashboard')); ?>"
                                       class="nav-item nav-link <?php echo e(request()->routeIs('reception.*') ? 'active' : ''); ?>">
                                        Panel
                                    </a>
                                <?php endif; ?>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>

                    
                    <div class="d-flex align-items-center me-5">
                        <?php if(auth()->guard()->check()): ?>
                            <a href="<?php echo e(route('minibar.carrito.index')); ?>" class="nav-link text-white">
                                <i class="fas fa-shopping-cart fa-lg"></i>
                            </a>
                        <?php else: ?>
                            <a href="<?php echo e(route('login')); ?>" class="nav-link text-white">
                                <i class="fas fa-shopping-cart fa-lg"></i>
                            </a>
                        <?php endif; ?>
                    </div>

                </div>
            </nav>
        </div>
    </div>
</div>
<?php /**PATH C:\laragon\www\hotel-piloto-sam\resources\views/layouts/header.blade.php ENDPATH**/ ?>