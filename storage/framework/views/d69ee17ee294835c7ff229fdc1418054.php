<div class="container-fluid p-0 mb-5">
    <div id="header-carousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="5000">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img class="w-100" src="img/carousel-1.jpg" alt="Image">
                <div class="carousel-caption d-flex flex-column align-items-center justify-content-center">
                    <div class="p-3" style="max-width: 700px;">
                        <h6 class="section-title text-white text-uppercase mb-3 animated slideInDown">Comodidad y Lujo</h6>
                        <h1 class="display-3 text-white mb-4 animated slideInDown">Tenemos a dispocisión los mejores servicios</h1>
                        <a class="btn btn-primary py-md-3 px-md-5 me-3 animated slideInLeft"
                           href="<?php echo e(route('rooms.index')); ?>">Reserva Ahora</a>
                    </div>
                </div>
            </div>
            <div class="carousel-item">
                <img class="w-100" src="img/carousel-2.jpg" alt="Image">
                <div class="carousel-caption d-flex flex-column align-items-center justify-content-center">
                    <div class="p-3" style="max-width: 700px;">
                        <h6 class="section-title text-white text-uppercase mb-3 animated slideInDown">Modernas Instalaciones</h6>
                        <h1 class="display-3 text-white mb-4 animated slideInDown">Descubre lo mejor en comodidad con nosotros</h1>
                        <a class="btn btn-primary py-md-3 px-md-5 me-3 animated slideInLeft"
                           href="<?php echo e(route('rooms.index')); ?>">Reserva Ahora</a>
                    </div>
                </div>
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#header-carousel"
                data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Anterior</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#header-carousel"
                data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Siguiente</span>
        </button>
    </div>
</div>
<?php /**PATH C:\laragon\www\hotel-piloto-sam\resources\views/sections/carousel.blade.php ENDPATH**/ ?>