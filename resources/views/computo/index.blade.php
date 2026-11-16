@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-0">Laboratorio de Cómputo</h3>
            <p class="text-muted small mb-0">
                Sesión activa de: <strong>{{ Auth::user()->name }}</strong> ({{ strtoupper(Auth::user()->rol) }})
            </p>
        </div>

        @if(Auth::user()->rol === 'admin')
            <a href="{{ route('equipos.create') }}" class="btn btn-warning text-dark fw-bold">+ Agregar Equipo</a>
        @endif
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

    <!-- CATÁLOGO DE EQUIPOS -->
    <h5 class="fw-bold mb-3">Equipos del Laboratorio</h5>
    <div class="row g-3 mb-5">
        @forelse($equipos as $eq)
        <div class="col-md-4 col-lg-3">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-bold fs-5">{{ $eq->codigo }}</span>
                            @php
                                $badge = match($eq->estado) {
                                    'Disponible' => 'bg-success',
                                    'Ocupado' => 'bg-danger',
                                    'Mantenimiento' => 'bg-secondary',
                                };
                            @endphp
                            <span class="badge {{ $badge }}">{{ $eq->estado }}</span>
                        </div>
                        <p class="text-muted small mb-3">{{ $eq->descripcion }}</p>
                    </div>

                    <div>
                        @if($eq->estado === 'Disponible')
                            <form action="{{ route('computo.reservar', $eq->id) }}" method="POST" class="mb-2">
                                @csrf
                                <button type="submit" class="btn btn-primary btn-sm w-100 fw-bold">Reservar Equipo</button>
                            </form>
                        @else
                            <button class="btn btn-secondary btn-sm w-100 mb-2" disabled>No Disponible</button>
                        @endif

                        @if(Auth::user()->rol === 'admin')
                            <div class="d-flex gap-1">
                                <a href="{{ route('equipos.edit', $eq) }}" class="btn btn-outline-warning btn-sm w-50">Editar</a>
                                <form action="{{ route('equipos.destroy', $eq) }}" method="POST" class="w-50 m-0">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm w-100">Borrar</button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="alert alert-info">No hay equipos registrados en el laboratorio.</div>
        </div>
        @endforelse
    </div>

    <!-- HISTORIAL DE RESERVACIONES -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3">
            <h6 class="fw-bold mb-0">
                {{ Auth::user()->rol === 'admin' ? 'Todas las Reservaciones del Sistema' : 'Mis Reservaciones Privadas' }}
            </h6>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Folio</th>
                        <th>Equipo</th>
                        @if(Auth::user()->rol === 'admin')
                            <th>Usuario</th>
                        @endif
                        <th>Fecha y Hora</th>
                        <th>Estado</th>
                        <th class="text-end">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reservaciones as $res)
                    <tr>
                        <td>#{{ $res->id }}</td>
                        <td class="fw-bold">{{ $res->equipo->codigo }}</td>
                        @if(Auth::user()->rol === 'admin')
                            <td>{{ $res->usuario->name }} ({{ $res->usuario->email }})</td>
                        @endif
                        <td class="small">{{ $res->fecha_reserva->format('d/m/Y H:i:s') }}</td>
                        <td>
                            <span class="badge {{ $res->estado === 'Activa' ? 'bg-primary' : 'bg-secondary' }}">
                                {{ $res->estado }}
                            </span>
                        </td>
                        <td class="text-end">
                            @if($res->estado === 'Activa')
                                <form action="{{ route('computo.cancelar', $res) }}" method="POST" class="m-0">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Cancelar / Liberar</button>
                                </form>
                            @else
                                <span class="text-muted small">Sin acciones</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ Auth::user()->rol === 'admin' ? 6 : 5 }}" class="text-center text-muted py-4">
                            No hay reservaciones registradas.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection