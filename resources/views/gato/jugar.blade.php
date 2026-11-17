@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-dark text-white py-3 d-flex justify-content-between align-items-center">
                    <span class="fw-bold">Sala #{{ $partida->id }}</span>
                    <a href="{{ route('gato.index') }}" class="btn btn-sm btn-outline-light">Volver al Lobby</a>
                </div>

                <div class="card-body p-4 text-center">
                    <!-- MARCADOR DE JUGADORES -->
                    <div class="d-flex justify-content-between bg-light rounded p-2 mb-3 small">
                        <div>
                            <span class="badge bg-primary">X</span>
                            <strong>{{ $partida->jugadorX->name }}</strong>
                        </div>
                        <div>
                            <span class="badge bg-danger">O</span>
                            <strong id="nombre-jugador-o">{{ $partida->jugadorO?->name ?? 'Esperando rival...' }}</strong>
                        </div>
                    </div>

                    <!-- BANNER DE ESTADO / TURNO -->
                    <div id="banner-estado" class="alert alert-info fw-bold py-2 mb-3">
                        Cargando estado de la partida...
                    </div>

                    <!-- ALERTA DE ERROR EN TIRO -->
                    <div id="alerta-error" class="alert alert-danger small py-2 d-none"></div>

                    <!-- TABLERO 3x3 -->
                    <div class="d-grid mx-auto mb-3" style="grid-template-columns: repeat(3, 1fr); gap: 10px; max-width: 300px;">
                        @for($i = 0; $i < 9; $i++)
                            <button type="button"
                                    class="btn btn-outline-dark fw-bold celda-gato d-flex align-items-center justify-content-center"
                                    data-index="{{ $i }}"
                                    style="height: 90px; font-size: 2.5rem;">
                                {{ $partida->tablero[$i] ?? '' }}
                            </button>
                        @endfor
                    </div>

                    <p class="text-muted small mb-0">
                        Tu ficha:
                        @if(Auth::id() === $partida->jugador_x_id)
                            <span class="badge bg-primary">X</span>
                        @elseif(Auth::id() === $partida->jugador_o_id)
                            <span class="badge bg-danger">O</span>
                        @else
                            <span class="badge bg-secondary">Espectador</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const userId = {{ Auth::id() }};
    const jugadorXId = {{ $partida->jugador_x_id }};
    let jugadorOId = {{ $partida->jugador_o_id ?? 'null' }};
    const urlEstado = "{{ route('gato.estado', $partida) }}";
    const urlTirar = "{{ route('gato.tirar', $partida) }}";
    const csrfToken = "{{ csrf_token() }}";

    const celdas = document.querySelectorAll('.celda-gato');
    const banner = document.getElementById('banner-estado');
    const alertaError = document.getElementById('alerta-error');
    const nombreO = document.getElementById('nombre-jugador-o');

    let estadoActual = "{{ $partida->estado }}";
    let turnoActual = {{ $partida->turno_user_id ?? 'null' }};
    let tableroActual = @json($partida->tablero);

    function renderizar(data) {
        estadoActual = data.estado;
        turnoActual = data.turno_user_id;
        tableroActual = data.tablero;

        if (data.jugador_o_nombre) {
            nombreO.textContent = data.jugador_o_nombre;
        }

        // Actualizar banner superior
        if (estadoActual === 'Esperando') {
            banner.className = 'alert alert-warning fw-bold py-2 mb-3';
            banner.textContent = 'Esperando a que se una un oponente...';
        } else if (estadoActual === 'EnCurso') {
            if (turnoActual === userId) {
                banner.className = 'alert alert-success fw-bold py-2 mb-3';
                banner.textContent = '¡ES TU TURNO! Selecciona una casilla';
            } else {
                banner.className = 'alert alert-secondary fw-bold py-2 mb-3';
                banner.textContent = 'Turno del oponente... Esperando movimiento';
            }
        } else if (estadoActual === 'Finalizada') {
            if (data.es_empate) {
                banner.className = 'alert alert-dark fw-bold py-2 mb-3';
                banner.textContent = '¡EMPATE! Tablero lleno sin ganador';
            } else if (data.ganador_id === null) {
                banner.className = 'alert alert-warning fw-bold py-2 mb-3';
                banner.textContent = 'Partida cancelada por el Administrador';
            } else if (data.ganador_id === userId) {
                banner.className = 'alert alert-success fw-bold py-2 mb-3';
                banner.textContent = '¡FELICIDADES! GANASTE LA PARTIDA';
            } else {
                banner.className = 'alert alert-danger fw-bold py-2 mb-3';
                banner.textContent = 'Partida finalizada. Ganador: ' + (data.ganador_nombre ?? 'Oponente');
            }
        }

        // Pintar las 9 celdas
        celdas.forEach((btn, idx) => {
            const val = tableroActual[idx];
            btn.textContent = val ?? '';
            btn.classList.remove('text-primary', 'text-danger');

            if (val === 'X') btn.classList.add('text-primary');
            if (val === 'O') btn.classList.add('text-danger');

            // Habilitar botón solo si la partida está en curso, es tu turno y la celda está vacía
            const puedeTirar = (estadoActual === 'EnCurso' && turnoActual === userId && val === null);
            btn.disabled = !puedeTirar;
        });
    }

    // Enviar jugada al backend
    celdas.forEach(btn => {
        btn.addEventListener('click', async () => {
            const index = btn.getAttribute('data-index');
            alertaError.classList.add('d-none');
            btn.disabled = true;

            try {
                const res = await fetch(urlTirar, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ casilla: index })
                });

                const json = await res.json();

                if (!res.ok || !json.ok) {
                    alertaError.textContent = json.mensaje ?? 'Movimiento no válido.';
                    alertaError.classList.remove('d-none');
                    consultarEstado();
                    return;
                }

                consultarEstado();
            } catch (err) {
                alertaError.textContent = 'Error de conexión con el servidor.';
                alertaError.classList.remove('d-none');
            }
        });
    });

    // Sincronizar con el servidor cada 2 segundos mientras la partida no termine
    async function consultarEstado() {
        try {
            const res = await fetch(urlEstado, { headers: { 'Accept': 'application/json' } });
            if (res.ok) {
                const data = await res.json();
                renderizar(data);
            }
        } catch (e) {}
    }

    // Inicializar estado y arrancar polling
    renderizar({
        estado: estadoActual,
        tablero: tableroActual,
        turno_user_id: turnoActual,
        ganador_id: {{ $partida->ganador_id ?? 'null' }},
        ganador_nombre: @json($partida->ganador?->name),
        es_empate: {{ $partida->es_empate ? 'true' : 'false' }},
        jugador_o_nombre: @json($partida->jugadorO?->name)
    });

    setInterval(() => {
        if (estadoActual !== 'Finalizada') {
            consultarEstado();
        }
    }, 2000);
</script>
@endsection