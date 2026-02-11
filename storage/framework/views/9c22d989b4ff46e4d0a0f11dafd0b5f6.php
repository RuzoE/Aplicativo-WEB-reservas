<div class="col-auto col-md-3 col-xl-2 px-sm-2 px-0 bg-dark admin-sidebar-fixed">
  <div class="d-flex flex-column align-items-center align-items-sm-start px-3 pt-2 text-white min-vh-100">
    <a href="/" class="d-flex align-items-center pb-3 mb-md-0 me-md-auto text-white text-decoration-none">
      <span class="fs-5 d-none d-sm-inline">Menu</span>
    </a>

    <div class="dropdown pb-4">
      <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
        <img src="https://github.com/mdo.png" alt="hugenerd" width="30" height="30" class="rounded-circle">
        <span class="d-none d-sm-inline mx-1">Admin</span>
      </a>
      <ul class="dropdown-menu dropdown-menu-dark text-small shadow">
        <li>
          <form method="post" action="<?php echo e(route('logout')); ?>">
            <?php echo csrf_field(); ?>
            <button type="submit" class="btn btn-link dropdown-item">Salir</button>
          </form>
        </li>
      </ul>
    </div>

    <ul class="nav nav-pills flex-column mb-sm-auto mb-0 align-items-center align-items-sm-start" id="menu">
      <li class="nav-item">
        <a href="<?php echo e(route('home')); ?>" class="nav-link align-middle px-0 text-warning">
          <i class="fs-4 bi-house"></i> <span class="ms-1 d-none d-sm-inline">Inicio</span>
        </a>
      </li>

      <?php if((auth()->user()->hasRole('administrador') || auth()->user()->hasRole('reservas')) && !request()->routeIs('reception.*')): ?>
      <li>
        <a href="<?php echo e(route('admin.habitaciones.dashboard')); ?>" class="nav-link px-0 align-middle text-warning">
          <i class="fs-4 bi-speedometer"></i> <span class="ms-1 d-none d-sm-inline">Dashboard Reservas</span>
        </a>
      </li>
      <?php endif; ?>

      <?php if((auth()->user()->hasRole('administrador') || auth()->user()->hasRole('minibar')) && !request()->routeIs('reception.*')): ?>
      <li>
        <a href="<?php echo e(route('admin.minibar.dashboard')); ?>" class="nav-link px-0 align-middle text-warning">
          <i class="fs-4 bi-bar-chart"></i> <span class="ms-1 d-none d-sm-inline">Dashboard Minibar</span>
        </a>
      </li>
      <?php endif; ?>

      <?php if(auth()->user()->hasRole('administrador') || auth()->user()->hasRole('recepcion')): ?>
      <li class="nav-item mt-3 mb-2">
        <a href="<?php echo e(route('reception.dashboard')); ?>" class="nav-link px-0 align-middle text-warning">
          <i class="fs-4 bi-grid-3x3-gap-fill"></i>
          <span class="ms-1 d-none d-sm-inline">Dashboard Recepción</span>
        </a>
      </li>
      <?php endif; ?>

      
      <?php if(auth()->user()->hasAnyRole(['administrador','recepcion']) && request()->routeIs('reception.*')): ?>
      <li class="nav-item mb-2">
        <a href="<?php echo e(route('reception.dashboard')); ?>#checkin" class="nav-link px-0 align-middle text-warning">
          <i class="fs-4 bi-door-open-fill"></i>
          <span class="ms-1 d-none d-sm-inline">Check-in</span>
        </a>
      </li>

      <li class="nav-item mb-2">
        <a href="<?php echo e(route('reception.dashboard')); ?>#folio" class="nav-link px-0 align-middle text-warning">
          <i class="fs-4 bi-receipt"></i>
          <span class="ms-1 d-none d-sm-inline">Folio</span>
        </a>
      </li>

      <li class="nav-item mb-2">
        <a href="<?php echo e(route('reception.dashboard')); ?>#checkout" class="nav-link px-0 align-middle text-warning">
          <i class="fs-4 bi-box-arrow-in-right"></i>
          <span class="ms-1 d-none d-sm-inline">Check-out</span>
        </a>
      </li>
      <?php endif; ?>

      
      <?php if(auth()->user()->hasRole('administrador') && !request()->routeIs('reception.*')): ?>
      <li>
        <a href="<?php echo e(route('admin.empleados.index')); ?>" class="nav-link px-0 align-middle">
          <i class="fs-4 bi-people"></i> <span class="ms-1 d-none d-sm-inline">Empleados</span>
        </a>
      </li>
      <?php endif; ?>
    </ul>
  </div>
</div>
<?php /**PATH C:\laragon\www\hotel-piloto-sam\resources\views/admin/sidebar.blade.php ENDPATH**/ ?>