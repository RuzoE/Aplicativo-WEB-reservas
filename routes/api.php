<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\Habitaciones\DashboardController as HabitacionesDashboardController;
use App\Http\Controllers\Admin\Habitaciones\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\Habitaciones\RoomController as AdminRoomController;
use App\Http\Controllers\Admin\Habitaciones\RoomTypeController;
use App\Http\Controllers\Admin\Mantenimiento\MaintenanceController;
use App\Http\Controllers\Admin\Minibar\BebidaTypeController;
use App\Http\Controllers\Admin\Minibar\CompraController;
use App\Http\Controllers\Admin\Minibar\DashboardController as MinibarDashboardController;
use App\Http\Controllers\Admin\Minibar\MinibarProductController as MinibarProductAdminController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Api\AuthController as ApiAuthController;
use App\Http\Controllers\Api\MinibarProductController;
use App\Http\Controllers\Minibar\User\BebidaController;
use App\Http\Controllers\Minibar\User\CartController;
use App\Http\Controllers\Minibar\User\CatalogController;
use App\Http\Controllers\Minibar\User\CheckoutController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\Reception\CheckInController as ReceptionCheckInController;
use App\Http\Controllers\Reception\CheckOutController as ReceptionCheckOutController;
use App\Http\Controllers\Reception\DashboardController as ReceptionDashboardController;
use App\Http\Controllers\Reception\FolioController as ReceptionFolioController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::get('/health', function () {
    return response()->json([
        'ok' => true,
        'app' => 'Hotel Oasis API',
        'time' => now()->toISOString(),
    ], 200);
});

Route::get('/ping-db', function () {
    DB::select('SELECT 1');
    return response()->json(['db' => 'ok'], 200);
});

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Estas rutas permiten probar desde Postman los mismos flujos del módulo web
| usando Bearer tokens de Sanctum en vez de sesión + CSRF.
|
*/

Route::prefix('auth')->name('api.auth.')->group(function () {
    Route::post('register', [ApiAuthController::class, 'register'])->name('register');
    Route::post('login', [ApiAuthController::class, 'login'])->name('login');

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('me', [ApiAuthController::class, 'me'])->name('me');
        Route::get('user', [ApiAuthController::class, 'me'])->name('user');
        Route::post('logout', [ApiAuthController::class, 'logout'])->name('logout');
    });
});

Route::get('/', [PageController::class, 'index'])->name('api.home');
Route::get('/rooms', [PageController::class, 'list_rooms'])->name('api.rooms.index');
Route::post('/rooms', [PageController::class, 'search'])->name('api.rooms.search');
Route::match(['GET', 'POST'], '/search', [PageController::class, 'search'])->name('api.search');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profile', [PageController::class, 'showProfile'])->name('api.profile');
    Route::put('/profile', [PageController::class, 'updateProfile'])->name('api.profile.update');

    Route::post('/orders', [OrderController::class, 'store'])->name('api.orders.store');
    Route::get('/orders', [OrderController::class, 'index'])->name('api.orders.index');
});

// -------------------------------------------
// Públicos (solo lectura)
// -------------------------------------------
Route::apiResource('minibar-products', MinibarProductController::class)
    ->only(['index', 'show']);

// -------------------------------------------
// Protegidos (requieren token)
// -------------------------------------------
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('minibar-products', MinibarProductController::class)
        ->only(['store', 'update', 'destroy']);
});

Route::prefix('admin')
    ->name('api.admin.')
    ->middleware(['auth:sanctum', 'role:administrador,sanctum'])
    ->group(function () {
        Route::get('/', [AdminDashboardController::class, 'index'])->name('index');

        Route::resource('empleados', EmployeeController::class)
            ->except(['show'])
            ->names('empleados');

        Route::post('roles', [RoleController::class, 'store'])->name('roles.store');
        Route::post('empleados/{user}/roles', [EmployeeController::class, 'assignRole'])
            ->whereNumber('user')
            ->name('empleados.roles.assign');
    });

Route::prefix('admin/habitaciones')
    ->name('api.admin.habitaciones.')
    ->middleware(['auth:sanctum', 'role:administrador|reservas,sanctum'])
    ->group(function () {
        Route::get('dashboard', [HabitacionesDashboardController::class, 'index'])->name('dashboard');
        Route::resource('reservas', AdminOrderController::class);
        Route::resource('tipos-habitacion', RoomTypeController::class)->except('show');
        Route::resource('habitaciones', AdminRoomController::class)->except('show');
    });

Route::prefix('admin/minibar')
    ->name('api.admin.minibar.')
    ->middleware(['auth:sanctum', 'role:administrador|minibar,sanctum'])
    ->group(function () {
        Route::get('dashboard', [MinibarDashboardController::class, 'index'])->name('dashboard');

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

        Route::get('ventas', [CompraController::class, 'index'])->name('ventas.index');
        Route::get('ventas/{compra}', [CompraController::class, 'show'])
            ->whereNumber('compra')
            ->name('ventas.show');
    });

Route::prefix('reservas')
    ->name('api.reservas.')
    ->middleware(['auth:sanctum', 'role:administrador|reservas,sanctum'])
    ->group(function () {
        Route::get('dashboard', [HabitacionesDashboardController::class, 'index'])->name('dashboard');
    });

Route::prefix('minibar-admin')
    ->name('api.minibarAdmin.')
    ->middleware(['auth:sanctum', 'role:administrador|minibar,sanctum'])
    ->group(function () {
        Route::get('dashboard', [MinibarDashboardController::class, 'index'])->name('dashboard');
    });

Route::prefix('admin/mantenimiento')
    ->name('api.admin.mantenimiento.')
    ->middleware(['auth:sanctum', 'role:administrador|mantenimiento,sanctum'])
    ->group(function () {
        Route::get('dashboard', [MaintenanceController::class, 'dashboard'])->name('dashboard');
        Route::get('/', [MaintenanceController::class, 'index'])->name('index');
        Route::post('/', [MaintenanceController::class, 'store'])->name('store');
        Route::post('{order}/complete', [MaintenanceController::class, 'completeMaintenanceOrder'])->name('complete');
        Route::post('{order}/urgent', [MaintenanceController::class, 'markUrgent'])->name('urgent');
        Route::get('room/{room}/history', [MaintenanceController::class, 'showHistory'])->name('history');
    });

Route::prefix('minibar')->name('api.minibar.')->group(function () {
    Route::get('/', [CatalogController::class, 'landing'])->name('landing');
    Route::get('catalogo', [CatalogController::class, 'index'])->name('catalogo');
    Route::get('carrito', [CartController::class, 'index'])->name('carrito.index');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('carrito/add', [CartController::class, 'add'])->name('carrito.add');
        Route::post('carrito/remove', [CartController::class, 'remove'])->name('carrito.remove');
        Route::get('checkout', [CheckoutController::class, 'index'])->name('checkout');
        Route::post('checkout/pay', [CheckoutController::class, 'pay'])->name('checkout.pay');
    });

    Route::get('{bebida}', [BebidaController::class, 'show'])->name('bebida.show');
});

Route::prefix('reception')
    ->name('api.reception.')
    ->middleware(['auth:sanctum', 'role:administrador|reservas|recepcion,sanctum'])
    ->group(function () {
        Route::get('dashboard', [ReceptionDashboardController::class, 'index'])->name('dashboard');
        Route::post('search-reservation', [ReceptionCheckInController::class, 'search'])->name('checkin.search');
        Route::get('check-in/{reservation}', [ReceptionCheckInController::class, 'show'])->name('checkin.show');
        Route::post('check-in/{reservation}', [ReceptionCheckInController::class, 'store'])->name('checkin.store');
        Route::get('folio/active-guests', [ReceptionFolioController::class, 'getActiveGuests'])->name('folio.guests');
        Route::post('folio/search', [ReceptionFolioController::class, 'search'])->name('folio.search');
        Route::get('stay/{stay}/folio', [ReceptionFolioController::class, 'show'])->name('folio.show');
        Route::post('stay/{stay}/charges', [ReceptionFolioController::class, 'postCharge'])->name('folio.charge');
        Route::post('stay/{stay}/payments', [ReceptionFolioController::class, 'postPayment'])->name('folio.payment');
        Route::post('check-out/{stay}', [ReceptionCheckOutController::class, 'store'])->name('checkout.store');
    });
