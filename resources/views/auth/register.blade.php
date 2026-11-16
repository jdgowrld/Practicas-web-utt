@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <h4 class="text-center fw-bold mb-4">Crear Cuenta</h4>

                    <form action="{{ route('register.post') }}" method="POST" novalidate>
                        @csrf
                        <div class="mb-3">
                            <label class="form-label text-muted small fw-bold">Nombre Completo</label>
                            <input type="text" name="name" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   value="{{ old('name') }}" 
                                   
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-muted small fw-bold">Correo Electrónico</label>
                            <input type="email" name="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   value="{{ old('email') }}" 
                                   
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-muted small fw-bold">Contraseña</label>
                            <input type="password" name="password" 
                                   class="form-control @error('password') is-invalid @enderror" 
                                   placeholder="Mínimo 8 caracteres">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label text-muted small fw-bold">Confirmar Contraseña</label>
                            <input type="password" name="password_confirmation" 
                                   class="form-control" 
                                   placeholder="Repite tu contraseña">
                        </div>

                        <button type="submit" class="btn btn-primary w-100 fw-bold">Registrarse</button>
                    </form>

                    <div class="text-center mt-3">
                        <a href="{{ route('login') }}" class="text-decoration-none small text-secondary">
                            ¿Ya tienes cuenta? Inicia sesión aquí
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection