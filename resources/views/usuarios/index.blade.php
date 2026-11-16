@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-0">Gestión de Usuarios</h3>
            <p class="text-muted small mb-0">Práctica 1.2 - Control de acceso basado en roles</p>
        </div>
        <a href="{{ route('usuarios.create') }}" class="btn btn-primary fw-bold">+ Nuevo Usuario</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- TABLA DE USUARIOS ACTIVOS -->
    <div class="card shadow-sm border-0 mb-5">
        <div class="card-header bg-white py-3">
            <h6 class="fw-bold mb-0 text-primary">Usuarios Activos</h6>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Rol</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($usuariosActivos as $u)
                    <tr>
                        <td>{{ $u->id }}</td>
                        <td class="fw-bold">{{ $u->name }}</td>
                        <td>{{ $u->email }}</td>
                        <td><span class="badge bg-secondary text-uppercase">{{ $u->rol }}</span></td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-1">
                                <a href="{{ route('usuarios.show', $u) }}" class="btn btn-sm btn-outline-info">Ver</a>
                                
                                @if(Auth::user()->rol === 'admin' || $u->rol !== 'admin')
                                <a href="{{ route('usuarios.edit', $u) }}" class="btn btn-sm btn-outline-warning">Editar</a>
                                @endif

                                {{-- SOLO EL ADMIN VE LAS OPCIONES DE ELIMINACIÓN --}}
                                @if(Auth::user()->rol === 'admin' && $u->id !== Auth::id())
                                    <!-- Eliminación Lógica (Soft Delete) -->
                                    <form action="{{ route('usuarios.destroy', $u) }}" method="POST" class="m-0">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Deshabilitar usuario">
                                            Deshabilitar
                                        </button>
                                    </form>

                                    <!-- Eliminación Permanente (Force Delete) -->
                                    <form action="{{ route('usuarios.forceDelete', $u->id) }}" method="POST" class="m-0">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Eliminar definitivamente">
                                            Eliminar
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- PAPELERA DE RECICLAJE (EXCLUSIVA PARA ADMIN - SOFT DELETES) -->
    @if(Auth::user()->rol === 'admin')
    <div class="card shadow-sm border-0 border-top border-danger border-3">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h6 class="fw-bold mb-0 text-danger">Usuarios Deshabilitados (Soft Deletes)</h6>
            <span class="badge bg-danger">{{ $usuariosEliminados->count() }} en papelera</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Rol</th>
                        <th>Fecha Deshabilitado</th>
                        <th class="text-end">Acciones de Recuperación</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($usuariosEliminados as $eliminado)
                    <tr class="table-secondary text-muted">
                        <td>{{ $eliminado->id }}</td>
                        <td>{{ $eliminado->name }}</td>
                        <td>{{ $eliminado->email }}</td>
                        <td><span class="badge bg-dark text-uppercase">{{ $eliminado->rol }}</span></td>
                        <td>{{ $eliminado->deleted_at->format('d/m/Y H:i') }}</td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-1">
                                <!-- Restaurar -->
                                <form action="{{ route('usuarios.restore', $eliminado->id) }}" method="POST" class="m-0">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm btn-success">Restaurar</button>
                                </form>

                                <!-- Borrado Físico Definitivo -->
                                <form action="{{ route('usuarios.forceDelete', $eliminado->id) }}" method="POST" class="m-0">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Borrar Definitivo</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">No hay usuarios deshabilitados en este momento.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
@endsection