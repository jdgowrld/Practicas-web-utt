@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0">Detalle del Usuario</h5>
                    <span class="badge bg-primary text-uppercase">{{ $usuario->rol }}</span>
                </div>
                <div class="card-body p-4">
                    <ul class="list-group list-group-flush mb-4">
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="text-muted">ID de Registro:</span>
                            <span class="fw-bold">#{{ $usuario->id }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="text-muted">Nombre Completo:</span>
                            <span class="fw-bold">{{ $usuario->name }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="text-muted">Correo Electrónico:</span>
                            <span>{{ $usuario->email }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="text-muted">Fecha de Creación:</span>
                            <span>{{ $usuario->created_at->format('d/m/Y H:i') }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="text-muted">Última Actualización:</span>
                            <span>{{ $usuario->updated_at->format('d/m/Y H:i') }}</span>
                        </li>
                    </ul>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('usuarios.index') }}" class="btn btn-outline-secondary">Volver al Listado</a>
                        <a href="{{ route('usuarios.edit', $usuario) }}" class="btn btn-warning fw-bold">Editar Usuario</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection