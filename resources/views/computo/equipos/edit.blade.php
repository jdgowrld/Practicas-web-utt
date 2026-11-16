@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <h4 class="fw-bold mb-4">Editar Equipo: {{ $equipo->codigo }}</h4>

                    <form action="{{ route('equipos.update', $equipo) }}" method="POST" novalidate>
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">Código del Equipo</label>
                            <input type="text" name="codigo" class="form-control @error('codigo') is-invalid @enderror"
                                   value="{{ old('codigo', $equipo->codigo) }}">
                            @error('codigo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">Descripción / Especificaciones</label>
                            <input type="text" name="descripcion" class="form-control @error('descripcion') is-invalid @enderror"
                                   value="{{ old('descripcion', $equipo->descripcion) }}">
                            @error('descripcion') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-bold text-muted">Estado</label>
                            <select name="estado" class="form-select @error('estado') is-invalid @enderror">
                                <option value="Disponible" {{ old('estado', $equipo->estado) == 'Disponible' ? 'selected' : '' }}>Disponible</option>
                                <option value="Ocupado" {{ old('estado', $equipo->estado) == 'Ocupado' ? 'selected' : '' }}>Ocupado</option>
                                <option value="Mantenimiento" {{ old('estado', $equipo->estado) == 'Mantenimiento' ? 'selected' : '' }}>Mantenimiento</option>
                            </select>
                            @error('estado') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('computo.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-warning text-dark fw-bold">Actualizar Equipo</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection