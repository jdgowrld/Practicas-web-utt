@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-0">Juego del Gato (3x3)</h3>
            <p class="text-muted small mb-0">Práctica 1.6 - Partidas multijugador sincronizadas por turnos</p>
        </div>

        <form action="{{ route('gato.crear') }}" method="POST" class="m-0">
            @csrf
            <button type="submit" class="btn btn-danger fw-bold">+ Crear Nueva Sala (Ficha X)</button>
        </form>
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

    <div class="row g-4">
        <!-- SALAS ABIERTAS PARA UNIRSE -->
        <div class="col-md-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3">
                    <h6 class="fw-bold mb-0 text-primary">Salas Abiertas (Esperando Oponente)</h6>
                </div>
                <div class="card-body">
                    @forelse($salasDisponibles as $sala)
                        <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                            <div>
                                <span class="fw-bold">Sala #{{ $sala->id }}</span>
                                <span class="text-muted small d-block">Creada por: {{ $sala->jugadorX->name }}</span>
                            </div>
                            <form action="{{ route('gato.unirse', $sala->id) }}" method="POST" class="m-0">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success fw-bold">Unirse (Ficha O)</button>
                            </form>
                        </div>
                    @empty
                        <p class="text-muted small text-center my-4">No hay salas esperando oponente. ¡Crea una nueva!</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- MIS PARTIDAS ACTIVAS E HISTORIAL -->
        <div class="col-md-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3">
                    <h6 class="fw-bold mb-0">Mis Partidas</h6>
                </div>
                <div class="card-body">
                    @forelse($misPartidas as $p)
                        <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                            <div>
                                <span class="fw-bold">Sala #{{ $p->id }}</span>
                                <span class="badge {{ $p->estado === 'Finalizada' ? 'bg-secondary' : ($p->estado === 'EnCurso' ? 'bg-success' : 'bg-warning text-dark') }} ms-1">
                                    {{ $p->estado }}
                                </span>
                                <span class="text-muted small d-block">
                                    {{ $p->jugadorX->name }} (X) vs {{ $p->jugadorO?->name ?? 'Esperando...' }} (O)
                                </span>
                                @if($p->estado === 'Finalizada')
                                    <span class="small fw-bold text-primary">
                                        {{ $p->es_empate ? 'Resultado: Empate' : 'Ganador: ' . $p->ganador?->name }}
                                    </span>
                                @endif
                            </div>
                            <a href="{{ route('gato.jugar', $p) }}" class="btn btn-sm btn-outline-dark">
                                {{ $p->estado === 'Finalizada' ? 'Ver Tablero' : 'Entrar' }}
                            </a>
                        </div>
                    @empty
                        <p class="text-muted small text-center my-4">Aún no has participado en ninguna partida.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection