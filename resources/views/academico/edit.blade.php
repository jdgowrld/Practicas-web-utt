@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <h4 class="fw-bold mb-1">Editar Registro Académico</h4>
                    <p class="text-muted small mb-4">Alumno: <strong>{{ $academico->alumno->name }}</strong></p>

                    <form action="{{ route('academico.update', $academico) }}" method="POST" novalidate>
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">Número de Faltas</label>
                            <input type="number" name="faltas" class="form-control @error('faltas') is-invalid @enderror" value="{{ old('faltas', $academico->faltas) }}">
                            @error('faltas') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-bold text-muted">Calificación Numérica (0 a 10)</label>
                            <input type="number" step="0.01" name="calificacion" class="form-control @error('calificacion') is-invalid @enderror" value="{{ old('calificacion', $academico->calificacion) }}">
                            @error('calificacion') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('academico.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-warning fw-bold">Actualizar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection