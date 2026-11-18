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

    <div class="row g-4 mb-4">
        <!-- COLUMNA 1: SALAS ABIERTAS -->
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
                                <span class="text-muted small d-block">Anfitrión: {{ $sala->jugadorX?->name ?? 'Usuario Deshabilitado' }}</span>
                            </div>
                            <form action="{{ route('gato.unirse', $sala->id) }}" method="POST" class="m-0">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success fw-bold">Unirse (Ficha O)</button>
                            </form>
                        </div>
                    @empty
                        <p class="text-muted small text-center my-4">No hay salas esperando oponente en este momento.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- COLUMNA 2: JUGADORES DISPONIBLES EN EL SISTEMA -->
        <div class="col-md-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0 text-success">Jugadores Registrados</h6>
                    <span class="badge bg-light text-dark border">{{ $jugadoresDisponibles->count() }} jugadores</span>
                </div>
                <div class="card-body">
                    @forelse($jugadoresDisponibles as $jug)
                        <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                            <div>
                                <span class="fw-bold">{{ $jug->name }}</span>
                                <span class="text-muted small d-block">{{ $jug->email }}</span>
                            </div>
                            @if($jug->ocupado)
                                <span class="badge bg-warning text-dark">En Sala / Jugando</span>
                            @else
                                <span class="badge bg-success">Disponible</span>
                            @endif
                        </div>
                    @empty
                        <p class="text-muted small text-center my-4">No hay otros usuarios con rol Jugador registrados.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- TABLA DE PARTIDAS (TODAS PARA ADMIN / PROPIAS PARA JUGADOR) -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3">
            <h6 class="fw-bold mb-0">
                {{ Auth::user()->rol === 'admin' ? 'Monitor Global de Partidas (Administrador)' : 'Mis Partidas' }}
            </h6>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Sala</th>
                        <th>Jugador X</th>
                        <th>Jugador O</th>
                        <th>Estado</th>
                        <th>Resultado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($partidas as $p)
                    <tr>
                        <td class="fw-bold">#{{ $p->id }}</td>
                        <td>{{ $p->jugadorX?->name ?? 'Usuario Deshabilitado' }}</td>
                        <td>{{ $p->jugadorO?->name ?? ($p->estado === 'Esperando' ? 'Esperando...' : 'Usuario Deshabilitado') }}</td>
                        <td>
                            <span class="badge {{ $p->estado === 'Finalizada' ? 'bg-secondary' : ($p->estado === 'EnCurso' ? 'bg-success' : 'bg-warning text-dark') }}">
                                {{ $p->estado }}
                            </span>
                        </td>
                        <td class="small fw-bold">
                            @if($p->estado === 'Finalizada')
                                @if($p->es_empate)
                                    <span class="text-muted">Empate</span>
                                @elseif($p->ganador)
                                    <span class="text-primary">Ganó: {{ $p->ganador->name }}</span>
                                @else
                                    <span class="text-danger">Cancelada / Abandonada</span>
                                @endif
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-1">
                                <a href="{{ route('gato.jugar', $p) }}" class="btn btn-sm btn-outline-dark">
                                    {{ $p->estado === 'Finalizada' ? 'Ver Tablero' : 'Entrar' }}
                                </a>

                                @if(Auth::user()->rol === 'admin' && $p->estado !== 'Finalizada')
                                    <form action="{{ route('gato.cancelar', $p) }}" method="POST" class="m-0">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">Cancelar Sala</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">No hay partidas registradas.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3">
            {{ $partidas->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection