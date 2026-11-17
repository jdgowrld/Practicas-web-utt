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


    // PRÁCTICA 1.3: SISTEMA ACADÉMICO
    // Consulta general (Profesor/Admin ven tabla; Alumno ve solo su boleta)
    Route::middleware(['rol:admin,profesor,alumno'])->group(function () {
        Route::get('/academico', [App\Http\Controllers\CalificacionController::class, 'index'])->name('academico.index');
    });

    // Operaciones de escritura protegidas solo para Profesor (y Admin)
    Route::middleware(['rol:admin,profesor'])->group(function () {
        Route::get('/academico/crear', [App\Http\Controllers\CalificacionController::class, 'create'])->name('academico.create');
        Route::post('/academico', [App\Http\Controllers\CalificacionController::class, 'store'])->name('academico.store');
        Route::get('/academico/{academico}/editar', [App\Http\Controllers\CalificacionController::class, 'edit'])->name('academico.edit');
        Route::put('/academico/{academico}', [App\Http\Controllers\CalificacionController::class, 'update'])->name('academico.update');
        Route::delete('/academico/{academico}', [App\Http\Controllers\CalificacionController::class, 'destroy'])->name('academico.destroy');
    });


    // PRÁCTICA 1.4: GESTIÓN DE PRODUCTOS    
    // Consulta, filtros, ordenamiento y paginación (Admin y Empleado)
    Route::middleware(['rol:admin,empleado'])->group(function () {
        Route::get('/productos', [App\Http\Controllers\ProductoController::class, 'index'])->name('productos.index');
    });

    // Escritura exclusiva para Administrador
    Route::middleware(['rol:admin'])->group(function () {
        Route::get('/productos/crear', [App\Http\Controllers\ProductoController::class, 'create'])->name('productos.create');
        Route::post('/productos', [App\Http\Controllers\ProductoController::class, 'store'])->name('productos.store');
        Route::get('/productos/{producto}/editar', [App\Http\Controllers\ProductoController::class, 'edit'])->name('productos.edit');
        Route::put('/productos/{producto}', [App\Http\Controllers\ProductoController::class, 'update'])->name('productos.update');
        Route::delete('/productos/{producto}', [App\Http\Controllers\ProductoController::class, 'destroy'])->name('productos.destroy');
    });


    
    // PRÁCTICA 1.5: RESERVACIÓN DE EQUIPOS
    
    // Consulta, reserva y cancelación propia (Admin y Estudiante)
    Route::middleware(['rol:admin,alumno'])->group(function () {
    Route::get('/computo', [App\Http\Controllers\ReservacionController::class, 'index'])->name('computo.index');
    Route::post('/computo/reservar/{equipo}', [App\Http\Controllers\ReservacionController::class, 'reservar'])->name('computo.reservar');
    Route::patch('/computo/cancelar/{reservacion}', [App\Http\Controllers\ReservacionController::class, 'cancelar'])->name('computo.cancelar');
    });

    // Administración del catálogo de equipos (Exclusivo Admin)
    Route::middleware(['rol:admin'])->group(function () {
        Route::get('/computo/equipos/crear', [App\Http\Controllers\EquipoController::class, 'create'])->name('equipos.create');
        Route::post('/computo/equipos', [App\Http\Controllers\EquipoController::class, 'store'])->name('equipos.store');
        Route::get('/computo/equipos/{equipo}/editar', [App\Http\Controllers\EquipoController::class, 'edit'])->name('equipos.edit');
        Route::put('/computo/equipos/{equipo}', [App\Http\Controllers\EquipoController::class, 'update'])->name('equipos.update');
        Route::delete('/computo/equipos/{equipo}', [App\Http\Controllers\EquipoController::class, 'destroy'])->name('equipos.destroy');
    });


    // ==========================================
    // PRÁCTICA 1.6: JUEGO DEL GATO (3x3)
    // ==========================================
    Route::middleware(['rol:admin,jugador'])->group(function () {
        Route::get('/gato', [App\Http\Controllers\GatoController::class, 'index'])->name('gato.index');
        Route::post('/gato/crear', [App\Http\Controllers\GatoController::class, 'crear'])->name('gato.crear');
        Route::post('/gato/{partida}/unirse', [App\Http\Controllers\GatoController::class, 'unirse'])->name('gato.unirse');
        Route::get('/gato/{partida}/jugar', [App\Http\Controllers\GatoController::class, 'jugar'])->name('gato.jugar');
        Route::get('/gato/{partida}/estado', [App\Http\Controllers\GatoController::class, 'estado'])->name('gato.estado');
        Route::post('/gato/{partida}/tirar', [App\Http\Controllers\GatoController::class, 'tirar'])->name('gato.tirar');
    });

    // Cancelación / cierre forzado exclusivo para Admin
    Route::middleware(['rol:admin'])->group(function () {
        Route::delete('/gato/{partida}/cancelar', [App\Http\Controllers\GatoController::class, 'cancelarPartida'])->name('gato.cancelar');
    });

});