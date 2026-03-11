<?php

use Illuminate\Support\Facades\Route;

// -----------------------
// Controladores públicos
// -----------------------
use App\Http\Controllers\PageController;
use App\Http\Controllers\OrderController;

// Auth
use App\Http\Controllers\Auth\AuthController;

// Admin – Dashboard Principal
use App\Http\Controllers\Admin\AdminDashboardController;

// Admin – Reservas de habitaciones
use App\Http\Controllers\Admin\Habitaciones\DashboardController as HabitacionesDashboardController;
use App\Http\Controllers\Admin\Habitaciones\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\Habitaciones\RoomTypeController;
use App\Http\Controllers\Admin\Habitaciones\RoomController as AdminRoomController;

// Admin – Minibar
use App\Http\Controllers\Admin\Minibar\DashboardController as MinibarDashboardController;
use App\Http\Controllers\Admin\Minibar\BebidaTypeController;
use App\Http\Controllers\Admin\Minibar\MinibarProductController as MinibarProductAdminController;
use App\Http\Controllers\Admin\Minibar\CompraController;

// Admin – Empleados
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\RoleController;

// Minibar – Front de usuario (catálogo/checkout)
use App\Http\Controllers\Minibar\User\CatalogController;
use App\Http\Controllers\Minibar\User\BebidaController;
use App\Http\Controllers\Minibar\User\CartController;
use App\Http\Controllers\Minibar\User\CheckoutController;

// Recepción
use App\Http\Controllers\Reception\DashboardController as ReceptionDashboardController;
use App\Http\Controllers\Reception\CheckInController as ReceptionCheckInController;
use App\Http\Controllers\Reception\FolioController as ReceptionFolioController;
use App\Http\Controllers\Reception\CheckOutController as ReceptionCheckOutController;
use App\Http\Controllers\Admin\Mantenimiento\MaintenanceController;

/* |-------------------------------------------------------------------------- | Rutas públicas |-------------------------------------------------------------------------- */
Route::get('/', [PageController::class , 'index'])->name('home');

Route::get('/rooms', [PageController::class , 'list_rooms'])->name('rooms.index');
Route::post('/rooms', [PageController::class , 'search'])->name('rooms.search');

// Alias genérico de búsqueda
Route::match (['GET', 'POST'], '/search', [PageController::class , 'search'])->name('search');












/* |-------------------------------------------------------------------------- | Rutas protegidas (requieren sesión) |-------------------------------------------------------------------------- */
Route::middleware('auth')->group(function () {
    Route::get('/profile', [PageController::class , 'showProfile'])->name('profile');
    Route::put('/profile', [PageController::class , 'updateProfile'])->name('profile.update');

    Route::post('/orders', [OrderController::class , 'store'])->name('orders.store');
    Route::get('/orders', [OrderController::class , 'index'])->name('orders.index');
});

/* |-------------------------------------------------------------------------- | Autenticación (simple) |-------------------------------------------------------------------------- */
Route::controller(AuthController::class)->group(function () {
    Route::get('register', 'showRegistrationForm')->name('register');
    Route::post('register', 'register');
    Route::get('login', 'showLoginForm')->name('login');
    Route::post('login', 'login');
    Route::post('logout', 'logout')->name('logout');
});

/* |-------------------------------------------------------------------------- | ADMINISTRACIÓN (solo rol: administrador) |-------------------------------------------------------------------------- */
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'role:administrador,web'])
    ->group(function () {

        // Dashboard global admin (nuevo dashboard con 3 botones)
        Route::get('/', [AdminDashboardController::class , 'index'])->name('index');

        // ===== Empleados (CRUD + roles) =====
        Route::resource('empleados', EmployeeController::class)
            ->except(['show'])
            ->names('empleados');

        // Crear rol desde el panel lateral
        Route::post('roles', [RoleController::class , 'store'])->name('roles.store');

        // Asignar/actualizar rol a un empleado
        Route::post('empleados/{user}/roles', [EmployeeController::class , 'assignRole'])
            ->whereNumber('user')
            ->name('empleados.roles.assign');
    });

/* |-------------------------------------------------------------------------- | PANEL RESERVAS (administrador|reservas) |-------------------------------------------------------------------------- */
Route::prefix('admin/habitaciones')
    ->name('admin.habitaciones.')
    ->middleware(['auth', 'role:administrador|reservas,web'])
    ->group(function () {
        Route::get('dashboard', [HabitacionesDashboardController::class , 'index'])->name('dashboard');

        Route::resource('reservas', AdminOrderController::class);
        Route::resource('tipos-habitacion', RoomTypeController::class)->except('show');
        Route::resource('habitaciones', AdminRoomController::class)->except('show');
    });

/* |-------------------------------------------------------------------------- | PANEL MINIBAR (administrador|minibar) |-------------------------------------------------------------------------- */
Route::prefix('admin/minibar')
    ->name('admin.minibar.')
    ->middleware(['auth', 'role:administrador|minibar,web'])
    ->group(function () {
        // Dashboard del módulo Minibar
        Route::get('dashboard', [MinibarDashboardController::class , 'index'])->name('dashboard');

        Route::resource('bebida-types', BebidaTypeController::class);

        Route::prefix('bebida-types-no-alcoholicas')
            ->name('bebida-types-na.')
            ->group(function () {
                Route::get('/', [BebidaTypeController::class, 'indexNonAlcoholic'])->name('index');
                Route::get('/create', [BebidaTypeController::class, 'createNonAlcoholic'])->name('create');
                Route::post('/', [BebidaTypeController::class, 'storeNonAlcoholic'])->name('store');
                Route::get('/{bebida_type}/edit', [BebidaTypeController::class, 'editNonAlcoholic'])->name('edit');
                Route::put('/{bebida_type}', [BebidaTypeController::class, 'updateNonAlcoholic'])->name('update');
                Route::delete('/{bebida_type}', [BebidaTypeController::class, 'destroyNonAlcoholic'])->name('destroy');
            });

        Route::resource('bebidas', MinibarProductAdminController::class)
            ->parameters(['bebidas' => 'bebida'])
            ->names('bebidas');

        Route::get('ventas', [CompraController::class , 'index'])->name('ventas.index');
        Route::get('ventas/{compra}', [CompraController::class , 'show'])
            ->whereNumber('compra')->name('ventas.show');
    });

/* |-------------------------------------------------------------------------- | DASHBOARDS OPERATIVOS (empleados con rol administrador) |-------------------------------------------------------------------------- */
Route::prefix('reservas')
    ->name('reservas.')
    ->middleware(['auth', 'role:administrador|reservas,web'])
    ->group(function () {
        Route::get('dashboard', [HabitacionesDashboardController::class , 'index'])->name('dashboard');
    // ...rutas operativas de reservas
    });

Route::prefix('minibar-admin')
    ->name('minibarAdmin.')
    ->middleware(['auth', 'role:administrador|minibar,web'])
    ->group(function () {
        Route::get('dashboard', [MinibarDashboardController::class , 'index'])->name('dashboard');
    // ...rutas operativas de minibar
    });

/* |-------------------------------------------------------------------------- | PANEL MANTENIMIENTO (administrador|mantenimiento) |-------------------------------------------------------------------------- */
Route::prefix('admin/mantenimiento')
    ->name('admin.mantenimiento.')
    ->middleware(['auth', 'role:administrador|mantenimiento,web'])
    ->group(function () {
        Route::get('dashboard', [MaintenanceController::class , 'dashboard'])->name('dashboard');
        Route::get('/', [MaintenanceController::class , 'index'])->name('index');
        Route::post('/', [MaintenanceController::class , 'store'])->name('store');
        Route::post('{order}/complete', [MaintenanceController::class , 'completeMaintenanceOrder'])->name('complete');
        Route::post('{order}/urgent', [MaintenanceController::class , 'markUrgent'])->name('urgent');
        Route::get('room/{room}/history', [MaintenanceController::class , 'showHistory'])->name('history');
    });

/* |-------------------------------------------------------------------------- | MINIBAR (front público/cliente) |-------------------------------------------------------------------------- */
Route::prefix('minibar')->name('minibar.')->group(function () {

    // Landing + catálogo
    Route::get('/', [CatalogController::class , 'landing'])->name('landing');
    Route::get('catalogo', [CatalogController::class , 'index'])->name('catalogo');

    // Carrito sin login
    Route::get('carrito', [CartController::class , 'index'])->name('carrito.index');

    // Acciones protegidas (checkout)
    Route::middleware('auth')->group(function () {
            Route::post('carrito/add', [CartController::class , 'add'])->name('carrito.add');
            Route::post('carrito/remove', [CartController::class , 'remove'])->name('carrito.remove');
            Route::get('checkout', [CheckoutController::class , 'index'])->name('checkout');
            Route::post('checkout/pay', [CheckoutController::class , 'pay'])->name('checkout.pay');
        }
        );

        // Detalle de bebida – dejar al final para no tapar rutas anteriores
        Route::get('{bebida}', [BebidaController::class , 'show'])->name('bebida.show');
    });

/* |-------------------------------------------------------------------------- | RECEPCIÓN (dashboard & front desk operations) |-------------------------------------------------------------------------- */
Route::prefix('reception')
    ->name('reception.')
    ->middleware(['auth', 'role:administrador|reservas|recepcion,web'])
    ->group(function () {
        Route::get('dashboard', [ReceptionDashboardController::class , 'index'])->name('dashboard');
        Route::post('search-reservation', [ReceptionCheckInController::class , 'search'])->name('checkin.search');
        Route::get('check-in/{reservation}', [ReceptionCheckInController::class , 'show'])->name('checkin.show');
        Route::post('check-in/{reservation}', [ReceptionCheckInController::class , 'store'])->name('checkin.store');
        Route::get('folio/active-guests', [ReceptionFolioController::class , 'getActiveGuests'])->name('folio.guests');
        Route::post('folio/search', [ReceptionFolioController::class , 'search'])->name('folio.search');
        Route::get('stay/{stay}/folio', [ReceptionFolioController::class , 'show'])->name('folio.show');
        Route::post('stay/{stay}/charges', [ReceptionFolioController::class , 'postCharge'])->name('folio.charge');
        Route::post('stay/{stay}/payments', [ReceptionFolioController::class , 'postPayment'])->name('folio.payment');
        Route::post('check-out/{stay}', [ReceptionCheckOutController::class , 'store'])->name('checkout.store');
    });
