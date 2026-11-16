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

    //  rutas de las Prácticas 1.2 a 1.6

    // PRÁCTICA 1.2: CRUD DE USUARIOS
    
    
    // Rutas permitidas para Admin y Empleado (Lectura, Creación y Edición)
    Route::middleware(['rol:admin,empleado'])->group(function () {
        Route::get('/usuarios', [App\Http\Controllers\UserController::class, 'index'])->name('usuarios.index');
        Route::get('/usuarios/crear', [App\Http\Controllers\UserController::class, 'create'])->name('usuarios.create');
        Route::post('/usuarios', [App\Http\Controllers\UserController::class, 'store'])->name('usuarios.store');
        Route::get('/usuarios/{usuario}', [App\Http\Controllers\UserController::class, 'show'])->name('usuarios.show');
        Route::get('/usuarios/{usuario}/editar', [App\Http\Controllers\UserController::class, 'edit'])->name('usuarios.edit');
        Route::put('/usuarios/{usuario}', [App\Http\Controllers\UserController::class, 'update'])->name('usuarios.update');
    });

    // Rutas EXCLUSIVAS para Admin (Soft Delete, Restore y Force Delete)
    Route::middleware(['rol:admin'])->group(function () {
        Route::delete('/usuarios/{usuario}', [App\Http\Controllers\UserController::class, 'destroy'])->name('usuarios.destroy');
        Route::patch('/usuarios/{id}/restaurar', [App\Http\Controllers\UserController::class, 'restore'])->name('usuarios.restore');
        Route::delete('/usuarios/{id}/forzar', [App\Http\Controllers\UserController::class, 'forceDelete'])->name('usuarios.forceDelete');
    });

});