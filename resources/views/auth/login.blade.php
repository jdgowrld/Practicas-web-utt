@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-5 col-lg-4">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <h4 class="text-center fw-bold mb-4">Iniciar Sesión</h4>

                    @if(isset($error_expirado))
                        <div class="alert alert-danger small">{{ $error_expirado }}</div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger small">{{ session('error') }}</div>
                    @endif

                    @if(session('success'))
                        <div class="alert alert-success small">{{ session('success') }}</div>
                    @endif

                    <form action="{{ route('login.post') }}" method="POST" novalidate>
                        @csrf
                        <div class="mb-3">
                            <label class="form-label text-muted small fw-bold">Correo Electrónico</label>
                            <input type="email" name="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   value="{{ old('email') }}" 
                                   placeholder="usuario@utt.edu.mx">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label text-muted small fw-bold">Contraseña</label>
                            <input type="password" name="password" 
                                   class="form-control @error('password') is-invalid @enderror" 
                                   placeholder="">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary w-100 fw-bold">Entrar</button>
                    </form>

                    <div class="text-center mt-3">
                        <a href="{{ route('register') }}" class="text-decoration-none small text-secondary">
                            ¿No tienes cuenta? Regístrate aquí
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection