<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MinibarProductController;
use Illuminate\Support\Facades\DB;

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
| Aquí registramos las rutas del API. Estas rutas se cargan automáticamente
| con el middleware "api", lo que significa que no requieren token CSRF.
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
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
