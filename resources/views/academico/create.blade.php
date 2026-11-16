@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <h4 class="fw-bold mb-4">Registrar Calificación</h4>

                    <form action="{{ route('academico.store') }}" method="POST" novalidate>
                        @csrf

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">Seleccionar Alumno</label>
                            <select name="user_id" class="form-select @error('user_id') is-invalid @enderror">
                                <option value="">Elige un alumno...</option>
                                @foreach($alumnosDisponibles as $al)
                                    <option value="{{ $al->id }}" {{ old('user_id') == $al->id ? 'selected' : '' }}>
                                        {{ $al->name }} ({{ $al->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('user_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">Número de Faltas</label>
                            <input type="number" name="faltas" class="form-control @error('faltas') is-invalid @enderror" value="{{ old('faltas', 0) }}">
                            @error('faltas') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-bold text-muted">Calificación Numérica (0 a 10)</label>
                            <input type="number" step="0.01" name="calificacion" class="form-control @error('calificacion') is-invalid @enderror" value="{{ old('calificacion') }}" placeholder="Ej. 9.5">
                            @error('calificacion') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('academico.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-success fw-bold">Guardar Registro</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection