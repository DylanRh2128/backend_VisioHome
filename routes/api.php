<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\AgenteController;
use App\Http\Controllers\DisponibilidadController;
use App\Http\Controllers\InmobiliariaController;
use App\Http\Controllers\PropiedadController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\DashboardController;

use App\Http\Controllers\Api\FavoriteController;
use App\Http\Controllers\Api\UserDashboardController;
use App\Http\Controllers\Api\SearchController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\CitaController;
use App\Http\Controllers\Api\ConfigurationController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\Api\WebhookController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\PasswordResetController;

/*
|--------------------------------------------------------------------------
| 🔓 RUTAS PÚBLICAS
|--------------------------------------------------------------------------
*/

Route::post('/auth/login',   [AuthController::class, 'login']);
Route::post('/auth/logout',  [AuthController::class, 'logout']);
Route::post('/auth/refresh', [AuthController::class, 'refresh']);
Route::post('/register', [AuthController::class, 'register']);

Route::get('/auth/google/token', function (Request $request) {
    $token = $request->cookie('jwt_token');
    if (!$token) return response()->json(['error' => 'Unauthorized'], 401);
    return response()->json(['token' => $token]);
});

// 📁 Manejo de Archivos y 3D
Route::get('/files/{path}', function ($path) {
    if (str_contains($path, '../') || str_contains($path, '..\\')) {
        abort(403);
    }
    $absolutePath = storage_path('app/public/' . $path);
    if (!file_exists($absolutePath)) {
        abort(404);
    }
    $extension = pathinfo($absolutePath, PATHINFO_EXTENSION);
    $mimeType = match (strtolower($extension)) {
        'gltf' => 'model/gltf+json',
        'glb' => 'model/gltf-binary',
        'bin' => 'application/octet-stream',
        'png' => 'image/png',
        'jpg', 'jpeg' => 'image/jpeg',
        'gif' => 'image/gif',
        'webp' => 'image/webp',
        default => mime_content_type($absolutePath) ?: 'application/octet-stream',
    };
    return response()->file($absolutePath, [
        'Content-Type' => $mimeType,
        'Access-Control-Allow-Origin' => '*',
        'Access-Control-Allow-Methods' => 'GET, OPTIONS',
    ]);
})->where('path', '.*');

Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink']);
Route::post('/reset-password',  [PasswordResetController::class, 'resetPassword']);

Route::post('/webhooks/mercadopago', [WebhookController::class, 'handleMercadoPago']);
Route::get('/system-status', [ConfigurationController::class, 'getStatus']);

// 🏠 Rutas de consulta pública
Route::get('/inmobiliarias', [InmobiliariaController::class, 'index']);
Route::get('/inmobiliarias/{id}', [InmobiliariaController::class, 'show']);

Route::get('/propiedades', [PropiedadController::class, 'index']);
Route::get('/propiedades/{id}', [PropiedadController::class, 'show']);
Route::get('/propiedades/{id}/modelo3d', [PropiedadController::class, 'getModelo3D']);

// 👥 Agentes (Solución al Error 404 en el Modal de Citas)
Route::get('/agentes', [AgenteController::class, 'index']); 
Route::get('/agentes/{id}', [\App\Http\Controllers\Api\AgenteController::class, 'show']);

/*
|--------------------------------------------------------------------------
| 🔐 RUTAS PROTEGIDAS (JWT)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:api'])->group(function () {

    // Info básica del usuario
    Route::get('/user', fn(Request $request) => response()->json(['user' => $request->user()]));

    /*
    |--------------------------------------------------------------------------
    | 👤 USER (CLIENTE)
    |--------------------------------------------------------------------------
    */
    Route::prefix('user')->group(function () {

        Route::get('/dashboard', [UserDashboardController::class, 'getDashboardData']);
        Route::get('/search', [SearchController::class, 'search']);
        Route::get('/agentes/{docAgente}/disponibilidad', [DisponibilidadController::class, 'show']);

        // Perfil (Solución al Error 405 Method Not Allowed)
        Route::get('/profile', [ProfileController::class, 'show']); // Para cargar los datos
        Route::put('/profile', [ProfileController::class, 'update']); // Para actualizar
        Route::post('/profile/avatar', [ProfileController::class, 'uploadAvatar']);
        Route::post('/profile/cv', [ProfileController::class, 'uploadCV']);

        // Favoritos
        Route::get('/favorites', [FavoriteController::class, 'index']);
        Route::post('/favorites/toggle/{id}', [FavoriteController::class, 'toggle']);
        Route::get('/favorites/check/{id}', [FavoriteController::class, 'check']);

        // Comentarios
        Route::get('/comments/{idPropiedad}', [CommentController::class, 'getByProperty']);
        Route::post('/comments', [CommentController::class, 'store']);
        Route::delete('/comments/{id}', [CommentController::class, 'destroy']);

        // Citas
        Route::get('/appointments', [CitaController::class, 'index']);
        Route::post('/appointments', [CitaController::class, 'store']);
        Route::put('/appointments/cancel/{id}', [CitaController::class, 'cancel']);
        Route::get('/appointments/{id}/payment-link', [CitaController::class, 'getPaymentLink']);
        Route::put('/appointments/{id}/reschedule', [CitaController::class, 'reschedule']);
    });

    /*
    |--------------------------------------------------------------------------
    | 🧑‍💼 AGENTE
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:agente')->prefix('agente')->group(function () {
        Route::get('/stats', [\App\Http\Controllers\AgenteDashboardController::class, 'getStats']);
        Route::get('/citas', [CitaController::class, 'agenteIndex']);
        Route::put('/appointments/confirm/{id}', [CitaController::class, 'confirmarCita']);
        Route::put('/appointments/cancel/{id}', [CitaController::class, 'cancelarPorAgente']);
        Route::get('/me', fn(Request $request) => response()->json($request->user()));
        Route::get('/disponibilidades', [DisponibilidadController::class, 'index']);
        Route::post('/disponibilidades', [DisponibilidadController::class, 'store']);
        Route::delete('/disponibilidades/{id}', [DisponibilidadController::class, 'destroy']);
    });

    /*
    |--------------------------------------------------------------------------
    | 🧾 ADMIN
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:admin')->prefix('admin')->group(function () {
        // 📊 KPIs
        Route::get('/stats', [DashboardController::class, 'getStats']);
        Route::get('/stats/users', [DashboardController::class, 'getUserStats']);
        Route::get('/stats/global', [DashboardController::class, 'getGlobalStats']);

        // 💰 PAGOS
        Route::apiResource('pagos', PagoController::class);
        Route::get('/pagos/{id}/pdf', [PagoController::class, 'downloadPdf']);

        // 👥 CRUD
        Route::apiResource('usuarios', UsuarioController::class);
        Route::apiResource('agentes', AgenteController::class);
        Route::apiResource('propiedades', PropiedadController::class);

        // ⚙️ CONFIG
        Route::get('/configurations', [ConfigurationController::class, 'index']);
        Route::put('/configurations/{key}', [ConfigurationController::class, 'update']);

        // 📥 EXPORTS
        Route::get('/export/users', [\App\Http\Controllers\Api\ExportController::class, 'exportUsers']);
        Route::get('/export/agentes', [\App\Http\Controllers\Api\ExportController::class, 'exportAgentes']);
        Route::get('/export/invoices', [\App\Http\Controllers\Api\ExportController::class, 'exportInvoices']);
        Route::get('/export/propiedades', [\App\Http\Controllers\Api\ExportController::class, 'exportPropiedades']);
        Route::get('/export/summary', [\App\Http\Controllers\Api\ExportController::class, 'exportSummary']);

        // 📦 UPLOADS
        Route::post('/propiedades/{id}/upload-3d', [PropiedadController::class, 'upload3D']);
        Route::post('/propiedades/{id}/upload-3d-zip', [PropiedadController::class, 'upload3DZip']);
    });
});