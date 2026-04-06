<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\PaymentController;

Route::get('/', fn() => view('welcome'));

// ─────────────────────────────────────────────────────────────────────
// AUTH (JWT)
// ─────────────────────────────────────────────────────────────────────

Route::post('/login',    [AuthController::class, 'login']);
Route::post('/logout',   [AuthController::class, 'logout']);


// ─────────────────────────────────────────────────────────────────────
// GOOGLE OAUTH
// ─────────────────────────────────────────────────────────────────────

Route::get('/auth/google',          [GoogleAuthController::class, 'redirect']);
Route::get('/auth/google/callback', [GoogleAuthController::class, 'handleGoogleCallback']);

// ─────────────────────────────────────────────────────────────────────
// MERCADOPAGO REDIRECTS
// ─────────────────────────────────────────────────────────────────────

Route::get('/payments/success', [PaymentController::class, 'success']);
Route::get('/payments/failure', [PaymentController::class, 'failure']);
Route::get('/payments/pending', [PaymentController::class, 'pending']);

// ─────────────────────────────────────────────────────────────────────
// DEBUG (eliminar en producción)
// ─────────────────────────────────────────────────────────────────────

Route::get('/debug-session', function (Request $request) {
    return response()->json([
        'has_jwt_cookie' => $request->hasCookie('jwt_token'),
        'auth_check'     => auth('api')->check(),
        'user'           => auth('api')->user()?->correo,
    ]);
});

// ─────────────────────────────────────────────────────────────────────
// STORAGE — CORS fix para php artisan serve (sin symlink)
// ─────────────────────────────────────────────────────────────────────

Route::withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class])
    ->get('/storage/{path}', function ($path) {
        $file = storage_path('app/public/' . $path);

        if (!\Illuminate\Support\Facades\File::exists($file)) {
            abort(404);
        }

        $mimeType = \Illuminate\Support\Facades\File::mimeType($file);

        if (str_ends_with($path, '.gltf')) $mimeType = 'model/gltf+json';
        if (str_ends_with($path, '.glb'))  $mimeType = 'model/gltf-binary';
        if (str_ends_with($path, '.bin'))  $mimeType = 'application/octet-stream';

        return \Illuminate\Support\Facades\Response::make(
            \Illuminate\Support\Facades\File::get($file), 200
        )
        ->header('Content-Type', $mimeType)
        ->header('Access-Control-Allow-Origin', '*')
        ->header('Access-Control-Allow-Methods', 'GET, OPTIONS')
        ->header('Access-Control-Allow-Headers', '*');
    })->where('path', '.*');