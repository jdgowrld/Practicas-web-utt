<?php

namespace App\Http\Controllers;

use App\Models\Partida;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GatoController extends Controller
{
    // Lobby: Salas disponibles y mis partidas
    public function index()
    {
        $userId = Auth::id();

        // Salas esperando oponente (creadas por otros usuarios)
        $salasDisponibles = Partida::with('jugadorX')
            ->where('estado', 'Esperando')
            ->where('jugador_x_id', '!=', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

        // Mis partidas activas o recientes
        $misPartidas = Partida::with(['jugadorX', 'jugadorO', 'ganador'])
            ->where(function ($q) use ($userId) {
                $q->where('jugador_x_id', $userId)
                  ->orWhere('jugador_o_id', $userId);
            })
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();

        return view('gato.index', compact('salasDisponibles', 'misPartidas'));
    }

    // Crear una nueva sala (El creador siempre es 'X')
    public function crear()
    {
        $partida = Partida::create([
            'jugador_x_id' => Auth::id(),
            'turno_user_id' => Auth::id(),
            'tablero' => array_fill(0, 9, null),
            'estado' => 'Esperando',
        ]);

        return redirect()->route('gato.jugar', $partida)->with('success', 'Sala creada. Esperando a que se una un oponente...');
    }

    // Unirse a una sala abierta (El segundo jugador siempre es 'O')
    public function unirse($partidaId)
    {
        $userId = Auth::id();

        try {
            $partida = DB::transaction(function () use ($partidaId, $userId) {
                $sala = Partida::where('id', $partidaId)->lockForUpdate()->firstOrFail();

                if ($sala->estado !== 'Esperando' || $sala->jugador_o_id !== null) {
                    throw new Exception('Esta sala ya está llena o ya inició.');
                }

                if ($sala->jugador_x_id === $userId) {
                    throw new Exception('No puedes jugar contra ti mismo en la misma cuenta.');
                }

                $sala->jugador_o_id = $userId;
                $sala->estado = 'EnCurso';
                $sala->save();

                return $sala;
            });

            return redirect()->route('gato.jugar', $partida)->with('success', '¡Te uniste a la partida! Eres la ficha O.');
        } catch (Exception $e) {
            return redirect()->route('gato.index')->with('error', $e->getMessage());
        }
    }

    // Vista principal del tablero 3x3
    public function jugar(Partida $partida)
    {
        $userId = Auth::id();

        // Solo los dos participantes (o el Admin para supervisar) pueden ver la sala
        if (Auth::user()->rol !== 'admin' && $partida->jugador_x_id !== $userId && $partida->jugador_o_id !== $userId) {
            abort(403, 'No eres participante de esta partida.');
        }

        $partida->load(['jugadorX', 'jugadorO', 'ganador']);

        return view('gato.jugar', compact('partida'));
    }

    // Endpoint JSON para sincronización automática cada 2 segundos
    public function estado(Partida $partida)
    {
        $userId = Auth::id();

        if (Auth::user()->rol !== 'admin' && $partida->jugador_x_id !== $userId && $partida->jugador_o_id !== $userId) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $partida->load(['jugadorX', 'jugadorO', 'ganador']);

        return response()->json([
            'estado' => $partida->estado,
            'tablero' => $partida->tablero,
            'turno_user_id' => $partida->turno_user_id,
            'ganador_id' => $partida->ganador_id,
            'ganador_nombre' => $partida->ganador?->name,
            'es_empate' => $partida->es_empate,
            'jugador_o_nombre' => $partida->jugadorO?->name,
        ]);
    }

    // Ejecutar movimiento en una casilla (0 a 8)
    public function tirar(Request $request, $partidaId)
    {
        $request->validate([
            'casilla' => 'required|integer|min:0|max:8',
        ]);

        $userId = Auth::id();
        $casilla = (int) $request->casilla;

        try {
            $resultado = DB::transaction(function () use ($partidaId, $userId, $casilla) {
                $partida = Partida::where('id', $partidaId)->lockForUpdate()->firstOrFail();

                if ($partida->estado !== 'EnCurso') {
                    throw new Exception('La partida aún no inicia o ya ha finalizado.');
                }

                if ($partida->turno_user_id !== $userId) {
                    throw new Exception('No es tu turno. Espera a que juegue tu oponente.');
                }

                $tablero = $partida->tablero;

                if ($tablero[$casilla] !== null) {
                    throw new Exception('Esa casilla ya está ocupada.');
                }

                // Determinar ficha ('X' o 'O')
                $ficha = ($userId === $partida->jugador_x_id) ? 'X' : 'O';
                $tablero[$casilla] = $ficha;
                $partida->tablero = $tablero;

                // Evaluar victoria o empate
                if ($this->hayGanador($tablero, $ficha)) {
                    $partida->estado = 'Finalizada';
                    $partida->ganador_id = $userId;
                    $partida->turno_user_id = null;
                } elseif (!in_array(null, $tablero, true)) {
                    $partida->estado = 'Finalizada';
                    $partida->es_empate = true;
                    $partida->turno_user_id = null;
                } else {
                    // Cambiar turno al otro jugador
                    $partida->turno_user_id = ($userId === $partida->jugador_x_id)
                        ? $partida->jugador_o_id
                        : $partida->jugador_x_id;
                }

                $partida->save();

                return $partida;
            });

            return response()->json([
                'ok' => true,
                'estado' => $resultado->estado,
                'tablero' => $resultado->tablero,
                'turno_user_id' => $resultado->turno_user_id,
                'ganador_id' => $resultado->ganador_id,
                'es_empate' => $resultado->es_empate,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'ok' => false,
                'mensaje' => $e->getMessage(),
            ], 422);
        }
    }

    // Evaluar las 8 combinaciones ganadoras del 3x3
    private function hayGanador(array $t, string $ficha): bool
    {
        $lineas = [
            [0, 1, 2], [3, 4, 5], [6, 7, 8], // Horizontales
            [0, 3, 6], [1, 4, 7], [2, 5, 8], // Verticales
            [0, 4, 8], [2, 4, 6],            // Diagonales
        ];

        foreach ($lineas as [$a, $b, $c]) {
            if ($t[$a] === $ficha && $t[$b] === $ficha && $t[$c] === $ficha) {
                return true;
            }
        }

        return false;
    }
}