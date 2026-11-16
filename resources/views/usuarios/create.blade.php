@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <h4 class="fw-bold mb-4">Registrar Nuevo Usuario</h4>

                    <form action="{{ route('usuarios.store') }}" method="POST" novalidate>
                        @csrf

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">Nombre Completo</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}">
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">Correo Electrónico</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}">
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">Rol del Sistema</label>
                            <select name="rol" class="form-select @error('rol') is-invalid @enderror">
                                <option value="">Selecciona un rol...</option>
                                @if(Auth::user()->rol === 'admin')
                                    <option value="admin" {{ old('rol') == 'admin' ? 'selected' : '' }}>Administrador</option>
                                @endif
                                <option value="empleado" {{ old('rol') == 'empleado' ? 'selected' : '' }}>Empleado</option>
                                <option value="profesor" {{ old('rol') == 'profesor' ? 'selected' : '' }}>Profesor (Práctica 1.3)</option>
                                <option value="alumno" {{ old('rol') == 'alumno' ? 'selected' : '' }}>Alumno (Práctica 1.3)</option>
                                <option value="estudiante" {{ old('rol') == 'estudiante' ? 'selected' : '' }}>Estudiante (Práctica 1.5)</option>
                                <option value="jugador" {{ old('rol') == 'jugador' ? 'selected' : '' }}>Jugador (Práctica 1.6)</option>
                            </select>
                            @error('rol') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">Contraseña</label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror">
                            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-bold text-muted">Confirmar Contraseña</label>
                            <input type="password" name="password_confirmation" class="form-control">
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('usuarios.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-primary fw-bold">Guardar Usuario</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection