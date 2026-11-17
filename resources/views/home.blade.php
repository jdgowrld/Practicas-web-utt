@extends('layouts.app')

@section('content')
<div class="container">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show shadow-sm" role="alert">
            {{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="text-center mb-5">
        <h2 class="fw-bold">Panel Central de Prácticas</h2>
        <p class="text-muted">Selecciona el módulo al que deseas ingresar</p>
    </div>

    <div class="row g-4">
        <!-- Práctica 1.2 -->
        <div class="col-md-4">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body d-flex flex-column justify-content-between">
                    <div>
                        <span class="badge bg-primary mb-2">Práctica 1.2</span>
                        <h5 class="fw-bold">CRUD de Usuarios</h5>
                        <p class="text-muted small">Gestión de roles, permisos y eliminación lógica (Soft Deletes).</p>
                    </div>
                    <a href="{{ route('usuarios.index') }}" class="btn btn-primary w-100 fw-bold">Entrar al Módulo</a>
                </div>
            </div>
        </div>

        <!-- Práctica 1.3 -->
        <div class="col-md-4">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body d-flex flex-column justify-content-between">
                    <div>
                        <span class="badge bg-success mb-2">Práctica 1.3</span>
                        <h5 class="fw-bold">Sistema Académico</h5>
                        <p class="text-muted small">Calificaciones, faltas y equivalencias automáticas (NA, SA, DE, AU).</p>
                    </div>
                    <a href="{{ route('academico.index') }}" class="btn btn-success w-100 fw-bold">Entrar al Módulo</a>
                </div>
            </div>
        </div>

        <!-- Práctica 1.4 -->
        <div class="col-md-4">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body d-flex flex-column justify-content-between">
                    <div>
                        <span class="badge bg-info text-dark mb-2">Práctica 1.4</span>
                        <h5 class="fw-bold">Gestión de Productos</h5>
                        <p class="text-muted small">Catálogo con paginación dinámica, filtros combinados y ordenamiento.</p>
                    </div>
                    <a href="{{ route('productos.index') }}" class="btn btn-info text-dark w-100 fw-bold">Entrar al Módulo</a>
                </div>
            </div>
        </div>

        <!-- Práctica 1.5 -->
        <div class="col-md-4">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body d-flex flex-column justify-content-between">
                    <div>
                        <span class="badge bg-warning text-dark mb-2">Práctica 1.5</span>
                        <h5 class="fw-bold">Reservación de Equipos</h5>
                        <p class="text-muted small">Sesiones independientes y control de concurrencia en reservas.</p>
                    </div>
                    <a href="{{ route('computo.index') }}" class="btn btn-warning text-dark w-100 fw-bold">Entrar al Módulo</a>
                </div>
            </div>
        </div>

        <!-- Práctica 1.6 -->
        <div class="col-md-4">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body d-flex flex-column justify-content-between">
                    <div>
                        <span class="badge bg-danger mb-2">Práctica 1.6</span>
                        <h5 class="fw-bold">Juego del Gato (3x3)</h5>
                        <p class="text-muted small">Partidas multijugador por turnos con sincronización en base de datos.</p>
                    </div>
                    <a href="{{ route('gato.index') }}" class="btn btn-danger w-100 fw-bold">Entrar al Módulo</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection