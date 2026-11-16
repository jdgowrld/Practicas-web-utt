@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-5 col-lg-4">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4 text-center">
                    <h4 class="fw-bold mb-2">Verificación 2FA</h4>
                    <p class="text-muted small mb-4">
                        Enviamos un código de 6 dígitos a tu correo. Tienes <strong>5 minutos</strong> para validarlo.
                    </p>

                    @if(session('error'))
                        <div class="alert alert-danger small">{{ session('error') }}</div>
                    @endif

                    <form action="{{ route('admin.2fa.verificar') }}" method="POST" novalidate>
                        @csrf
                        <div class="mb-4">
                            <input type="text" name="codigo" maxlength="6"
                                   class="form-control form-control-lg text-center fw-bold @error('codigo') is-invalid @enderror" 
                                   style="letter-spacing: 0.4em;"
                                   placeholder="000000" 
                                   value="{{ old('codigo') }}">
                            @error('codigo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary w-100 fw-bold mb-2">Validar Código</button>
                    </form>

                    <form action="{{ route('logout') }}" method="POST" class="mt-2">
                        @csrf
                        <button type="submit" class="btn btn-link btn-sm text-secondary text-decoration-none">
                            Cancelar y volver al login
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection