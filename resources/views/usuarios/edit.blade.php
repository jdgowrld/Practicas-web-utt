@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <h4 class="fw-bold mb-4">Editar Usuario: {{ $usuario->name }}</h4>

                    <form action="{{ route('usuarios.update', $usuario) }}" method="POST" novalidate>
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">Nombre Completo</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $usuario->name) }}">
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">Correo Electrónico</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $usuario->email) }}">
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        @if(Auth::user()->rol === 'admin')
                            <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">Rol del Sistema</label>
                            <select name="rol" class="form-select @error('rol') is-invalid @enderror">
                                <option value="admin" {{ old('rol', $usuario->rol) == 'admin' ? 'selected' : '' }}>Administrador</option>
                                <option value="empleado" {{ old('rol', $usuario->rol) == 'empleado' ? 'selected' : '' }}>Empleado</option>
                                <option value="profesor" {{ old('rol', $usuario->rol) == 'profesor' ? 'selected' : '' }}>Profesor</option>
                                <option value="alumno" {{ old('rol', $usuario->rol) == 'alumno' ? 'selected' : '' }}>Alumno</option>
                                <option value="estudiante" {{ old('rol', $usuario->rol) == 'estudiante' ? 'selected' : '' }}>Estudiante</option>
                                <option value="jugador" {{ old('rol', $usuario->rol) == 'jugador' ? 'selected' : '' }}>Jugador</option>
                            </select>
                            @error('rol') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        @endif

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">Nueva Contraseña (Opcional)</label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Dejar en blanco para mantener la actual">
                            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-bold text-muted">Confirmar Nueva Contraseña</label>
                            <input type="password" name="password_confirmation" class="form-control">
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('usuarios.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-warning fw-bold">Actualizar Datos</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection