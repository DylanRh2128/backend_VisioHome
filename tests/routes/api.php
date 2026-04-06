<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\AgenteController;
use App\Http\Controllers\InmobiliariaController;
use App\Http\Controllers\PropiedadController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AppointmentController;
// 🔓 AUTH
Route::post('/auth/login',  [\App\Http\Controllers\Auth\AuthController::class, 'login']);
Route::post('/auth/logout', [\App\Http\Controllers\Auth\AuthController::class, 'logout']);

Route::middleware('auth:sanctum')->group(function () {
    // Admin & General Stats
    Route::get('/stats', [DashboardController::class, 'getStats']);
    Route::get('/stats/users', [DashboardController::class, 'getUserStats']);
    Route::get('/stats/global', [DashboardController::class, 'getGlobalStats']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Módulo Usuario - Especializado
    Route::prefix('user')->group(function () {
        Route::get('/dashboard-data', [\App\Http\Controllers\Api\UserDashboardController::class, 'getDashboardData']);
        Route::get('/search', [\App\Http\Controllers\Api\SearchController::class, 'search']);
        Route::get('/cart', [\App\Http\Controllers\CartController::class, 'index']);
        Route::post('/cart/add', [\App\Http\Controllers\CartController::class, 'add']);
        Route::delete('/cart/remove/{id}', [\App\Http\Controllers\CartController::class, 'remove']);
        Route::get('/appointments', [\App\Http\Controllers\AppointmentController::class, 'index']);
        Route::get('/comments/{idPropiedad}', [\App\Http\Controllers\Api\CommentController::class, 'getComments']);
        Route::post('/comments', [\App\Http\Controllers\Api\CommentController::class, 'store']);
        Route::post('/checkout', [\App\Http\Controllers\Api\CheckoutController::class, 'process']);

        // Profile
        Route::get('/profile', [\App\Http\Controllers\Api\ProfileController::class, 'show']);
        Route::put('/profile', [\App\Http\Controllers\Api\ProfileController::class, 'update']);
        Route::post('/profile/avatar', [\App\Http\Controllers\Api\ProfileController::class, 'uploadAvatar']);

        // Favorites
        Route::get('/favorites', [\App\Http\Controllers\Api\FavoriteController::class, 'index']);
        Route::post('/favorites/toggle/{id}', [\App\Http\Controllers\Api\FavoriteController::class, 'toggle']);
        Route::get('/favorites/check/{id}', [\App\Http\Controllers\Api\FavoriteController::class, 'check']);
    });

    // Resources
    Route::apiResource('usuarios', UsuarioController::class);
    Route::apiResource('agentes', AgenteController::class);
    Route::apiResource('inmobiliarias', InmobiliariaController::class);
    Route::apiResource('propiedades', PropiedadController::class);
    Route::apiResource('pagos', PagoController::class);
    Route::apiResource('citas', AppointmentController::class);
});
