<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

// Rutas Públicas (Solo Invitados)
Route::middleware('guest')->group(function () {
    Route::get('/registro', [AuthController::class, 'showRegistrationForm'])->name('register');
    Route::post('/registro', [AuthController::class, 'register'])->name('register.post');
    Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

// Rutas de Autenticación y Verificación 2FA
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/2fa/verificar', [AuthController::class, 'show2faForm'])->name('admin.2fa.vista');
    Route::post('/2fa/verificar', [AuthController::class, 'verificarCodigo'])->name('admin.2fa.verificar');

    Route::get('/2fa/enlace/{user}', [AuthController::class, 'verificarEnlace'])
        ->name('admin.2fa.verificar.enlace')
        ->middleware('signed');
});

// Bóveda Protegida (Requiere Auth + 2FA completado si es Admin)
Route::middleware(['auth', '2fa'])->group(function () {
    Route::get('/home', function () {
        return view('home');
    })->name('home');

    // Aquí iremos conectando las rutas de las Prácticas 1.2 a 1.6
});