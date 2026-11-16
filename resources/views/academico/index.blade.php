@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h3 class="fw-bold mb-0">Sistema Académico</h3>
            <p class="text-muted small mb-0">Práctica 1.3 - Gestión de calificaciones y equivalencias automáticas</p>
        </div>

        <div class="d-flex gap-2">
            <!-- Selector de Ordenamiento -->
            <form action="{{ route('academico.index') }}" method="GET" class="d-flex gap-2">
                <select name="orden" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="nombre_asc" {{ $orden == 'nombre_asc' ? 'selected' : '' }}>Nombre (A - Z)</option>
                    <option value="nombre_desc" {{ $orden == 'nombre_desc' ? 'selected' : '' }}>Nombre (Z - A)</option>
                    <option value="calificacion_desc" {{ $orden == 'calificacion_desc' ? 'selected' : '' }}>Calificación (Mayor a Menor)</option>
                    <option value="calificacion_asc" {{ $orden == 'calificacion_asc' ? 'selected' : '' }}>Calificación (Menor a Mayor)</option>
                    <option value="faltas_desc" {{ $orden == 'faltas_desc' ? 'selected' : '' }}>Faltas (Mayor a Menor)</option>
                    <option value="faltas_asc" {{ $orden == 'faltas_asc' ? 'selected' : '' }}>Faltas (Menor a Mayor)</option>
                </select>
            </form>

            <a href="{{ route('academico.create') }}" class="btn btn-success btn-sm fw-bold">+ Registrar Alumno</a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Nombre del Alumno</th>
                        <th class="text-center">Número de Faltas</th>
                        <th class="text-center">Calificación Numérica</th>
                        <th class="text-center">Equivalencia</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($registros as $reg)
                    <tr>
                        <td class="fw-bold">{{ $reg->alumno->name }}</td>
                        <td class="text-center">{{ $reg->faltas }}</td>
                        <td class="text-center fw-bold">{{ number_format($reg->calificacion, 2) }}</td>
                        <td class="text-center">
                            @php
                                $badgeColor = match($reg->equivalencia) {
                                    'AU' => 'bg-primary',
                                    'DE' => 'bg-success',
                                    'SA' => 'bg-warning text-dark',
                                    'NA' => 'bg-danger',
                                };
                            @endphp
                            <span class="badge {{ $badgeColor }} fs-6">{{ $reg->equivalencia }}</span>
                        </td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-1">
                                <a href="{{ route('academico.edit', $reg) }}" class="btn btn-sm btn-outline-warning">Editar</a>
                                <form action="{{ route('academico.destroy', $reg) }}" method="POST" class="m-0">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Eliminar</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">No hay registros académicos capturados aún.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection