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
use App\Http\Controllers\Admin\Minibar\NotificacionController;

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
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\AuditoriaController;
use App\Http\Controllers\Admin\BackupController;

/* |-------------------------------------------------------------------------- | Rutas públicas |-------------------------------------------------------------------------- */
Route::get('/', [PageController::class , 'index'])->name('home');

Route::get('/rooms', [PageController::class , 'list_rooms'])->name('rooms.index');
Route::post('/rooms', [PageController::class , 'search'])->name('rooms.search');

// Alias genérico de búsqueda
Route::match (['GET', 'POST'], '/search', [PageController::class , 'search'])->name('search');












// Rutas públicas relacionadas con reservas y pago de anticipo
Route::post('/orders', [OrderController::class , 'store'])->name('orders.store');
Route::get('/orders/payment/{token}', [OrderController::class, 'paymentPage'])->name('orders.payment');
Route::post('/orders/payment/{token}/confirm', [OrderController::class, 'confirmPayment'])->name('orders.confirm_payment');

/* |-------------------------------------------------------------------------- | Rutas protegidas (requieren sesión) |-------------------------------------------------------------------------- */
Route::middleware('auth')->group(function () {
    Route::get('/profile', [PageController::class , 'showProfile'])->name('profile');
    Route::put('/profile', [PageController::class , 'updateProfile'])->name('profile.update');

    Route::get('/orders', [OrderController::class , 'index'])->name('orders.index');

    Route::get('/orders/{user_order}/edit', [OrderController::class, 'edit'])->name('orders.edit');
    Route::put('/orders/{user_order}', [OrderController::class, 'update'])->name('orders.update');
    Route::delete('/orders/{user_order}', [OrderController::class, 'destroy'])->name('orders.destroy');
});

/* |-------------------------------------------------------------------------- | Autenticación (simple) |-------------------------------------------------------------------------- */
Route::controller(AuthController::class)->group(function () {
    Route::get('register', 'showRegistrationForm')->name('register');
    Route::post('register', 'register')->middleware('throttle:auth-register');
    Route::get('login', 'showLoginForm')->name('login');
    Route::post('login', 'login')->middleware('throttle:auth-login');
    Route::post('logout', 'logout')->name('logout');
});

/* |-------------------------------------------------------------------------- | ADMINISTRACIÓN (solo rol: administrador) |-------------------------------------------------------------------------- */
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'role:administrador,web', 'audit.admin'])
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

        // Informe General – vista web ejecutiva
        Route::get('informe-general/preview', [ReportController::class, 'preview'])->name('report.preview');

        // Informe General (descarga PDF)
        Route::get('informe-general', [ReportController::class, 'download'])->name('report.download');

        // Auditoria del sistema
        Route::get('auditorias', [AuditoriaController::class, 'index'])->name('auditorias.index');

        // Backups del sistema
        Route::get('backups', [BackupController::class, 'index'])->name('backups.index');
        Route::get('backups/status', [BackupController::class, 'status'])->name('backups.status');
        Route::post('backups/generate', [BackupController::class, 'generate'])->name('backups.generate');
        Route::get('backups/download', [BackupController::class, 'download'])->name('backups.download');
        Route::delete('backups', [BackupController::class, 'destroy'])->name('backups.destroy');
        Route::put('backups/schedule', [BackupController::class, 'updateSchedule'])->name('backups.schedule');
        Route::post('backups/restore', [BackupController::class, 'restore'])->name('backups.restore');
        Route::post('backups/reset', [BackupController::class, 'resetStatus'])->name('backups.reset');

        // ── Diagnóstico de correo Brevo (temporal) ──
        Route::get('test-mail', function () {
            $config = [
                'mailer'       => config('mail.default'),
                'from_address' => config('mail.from.address'),
                'from_name'    => config('mail.from.name'),
                'brevo_key_set' => !empty(env('BREVO_API_KEY')),
            ];

            try {
                \Illuminate\Support\Facades\Mail::raw(
                    'Correo de prueba desde Hotel Oasis vía Brevo API - ' . now()->toDateTimeString(),
                    function ($msg) {
                        $msg->to(config('mail.from.address'))  // envía al mismo FROM como prueba
                            ->subject('✅ Test Brevo API - Hotel Oasis');
                    }
                );
                return response()->json([
                    'status'  => 'OK',
                    'message' => 'Correo enviado correctamente vía Brevo API',
                    'config'  => $config,
                ]);
            } catch (\Throwable $e) {
                return response()->json([
                    'status'  => 'ERROR',
                    'error'   => $e->getMessage(),
                    'class'   => get_class($e),
                    'config'  => $config,
                ], 500);
            }
        })->name('test-mail');

        // Aliases sin prefijo para compatibilidad legacy
        // Las rutas anteriores generan: admin.report.preview / admin.report.download
    });

/* |-------------------------------------------------------------------------- | PANEL RESERVAS (administrador|reservas) |-------------------------------------------------------------------------- */
Route::prefix('admin/habitaciones')
    ->name('admin.habitaciones.')
    ->middleware(['auth', 'role:administrador|reservas,web', 'audit.admin'])
    ->group(function () {
        Route::get('dashboard', [HabitacionesDashboardController::class , 'index'])->name('dashboard');

        Route::resource('reservas', AdminOrderController::class);
        Route::resource('tipos-habitacion', RoomTypeController::class)->except('show');
        Route::resource('habitaciones', AdminRoomController::class)->except('show');
    });

/* |-------------------------------------------------------------------------- | PANEL MINIBAR (administrador|minibar) |-------------------------------------------------------------------------- */
Route::prefix('admin/minibar')
    ->name('admin.minibar.')
    ->middleware(['auth', 'role:administrador|minibar,web', 'audit.admin'])
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

        Route::get('notificaciones', [NotificacionController::class, 'index'])->name('notificaciones.index');
    });

/* |-------------------------------------------------------------------------- | DASHBOARDS OPERATIVOS (empleados con rol administrador) |-------------------------------------------------------------------------- */
Route::prefix('reservas')
    ->name('reservas.')
    ->middleware(['auth', 'role:administrador|reservas,web', 'audit.admin'])
    ->group(function () {
        Route::get('dashboard', [HabitacionesDashboardController::class , 'index'])->name('dashboard');
    // ...rutas operativas de reservas
    });

Route::prefix('minibar-admin')
    ->name('minibarAdmin.')
    ->middleware(['auth', 'role:administrador|minibar,web', 'audit.admin'])
    ->group(function () {
        Route::get('dashboard', [MinibarDashboardController::class , 'index'])->name('dashboard');
    // ...rutas operativas de minibar
    });

/* |-------------------------------------------------------------------------- | PANEL MANTENIMIENTO (administrador|mantenimiento) |-------------------------------------------------------------------------- */
Route::prefix('admin/mantenimiento')
    ->name('admin.mantenimiento.')
    ->middleware(['auth', 'role:administrador|mantenimiento,web', 'audit.admin'])
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
    ->middleware(['auth', 'role:administrador|reservas|recepcion,web', 'audit.admin'])
    ->group(function () {
        Route::get('dashboard', [ReceptionDashboardController::class , 'index'])->name('dashboard');
        Route::post('search-reservation', [ReceptionCheckInController::class , 'search'])
            ->middleware('throttle:reception-sensitive')
            ->name('checkin.search');
        Route::get('check-in/{reservation}', [ReceptionCheckInController::class , 'show'])->name('checkin.show');
        Route::post('check-in/{reservation}', [ReceptionCheckInController::class , 'store'])
            ->middleware('throttle:reception-sensitive')
            ->name('checkin.store');
        Route::get('folio/active-guests', [ReceptionFolioController::class , 'getActiveGuests'])
            ->middleware('throttle:reception-sensitive')
            ->name('folio.guests');
        Route::post('folio/search', [ReceptionFolioController::class , 'search'])
            ->middleware('throttle:reception-sensitive')
            ->name('folio.search');
        Route::get('stay/{stay}/folio', [ReceptionFolioController::class , 'show'])->name('folio.show');
        Route::post('stay/{stay}/charges', [ReceptionFolioController::class , 'postCharge'])
            ->middleware('throttle:reception-sensitive')
            ->name('folio.charge');
        Route::post('stay/{stay}/payments', [ReceptionFolioController::class , 'postPayment'])
            ->middleware('throttle:reception-sensitive')
            ->name('folio.payment');
        Route::post('check-out/{stay}', [ReceptionCheckOutController::class , 'store'])
            ->middleware('throttle:reception-sensitive')
            ->name('checkout.store');
        Route::get('invoices/{invoice}/download', [ReceptionCheckOutController::class , 'download'])->name('invoices.download');

        // Walk-In Registrations
        Route::get('walk-in/api/availability', [\App\Http\Controllers\Reception\WalkInController::class, 'checkAvailability'])->name('walkin.availability');
        Route::get('walk-in', [\App\Http\Controllers\Reception\WalkInController::class , 'create'])->name('walkin.create');
        Route::post('walk-in', [\App\Http\Controllers\Reception\WalkInController::class , 'store'])
            ->middleware('throttle:reception-sensitive')
            ->name('walkin.store');

        // Stay Management (Linking Users)
        Route::get('users/search', [\App\Http\Controllers\Reception\StayController::class, 'searchUsers'])
            ->middleware('throttle:reception-sensitive')
            ->name('users.search');
        Route::post('stay/{stay}/link-user', [\App\Http\Controllers\Reception\StayController::class, 'linkUser'])
            ->middleware('throttle:reception-sensitive')
            ->name('stay.link_user');

        // Advance Payments & Room Assignment
        Route::get('anticipos', [\App\Http\Controllers\Reception\AdvanceController::class, 'index'])->name('anticipos.index');
        Route::get('asignacion/api/rooms-by-date', [\App\Http\Controllers\Reception\AssignmentController::class, 'roomsByDate'])->name('asignacion.rooms_by_date');
        Route::get('asignacion/{reserva?}', [\App\Http\Controllers\Reception\AssignmentController::class, 'index'])->name('asignacion.index');
        Route::post('asignacion/{reserva}/confirm/{room}', [\App\Http\Controllers\Reception\AssignmentController::class, 'assign'])
            ->middleware('throttle:reception-sensitive')
            ->name('asignacion.confirm');
    });
