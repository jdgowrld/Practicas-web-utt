<?php

namespace App\Http\Controllers;

use App\Models\Equipo;
use App\Models\Reservacion;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReservacionController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $equipos = Equipo::orderBy('codigo')->get();

        // El Admin ve todas las reservaciones; el Estudiante SOLO las suyas
        if ($user->rol === 'admin') {
            $reservaciones = Reservacion::with(['usuario', 'equipo'])
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            $reservaciones = Reservacion::with('equipo')
                ->where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return view('computo.index', compact('equipos', 'reservaciones'));
    }

    // Reservar un equipo con bloqueo a nivel de Usuario y Equipo (Cero TOCTOU)
    public function reservar($equipoId)
    {
        $userId = Auth::id();

        try {
            DB::transaction(function () use ($equipoId, $userId) {
                // 1. Bloqueamos la fila del usuario en 'users' para serializar sus propias solicitudes simultáneas
                \App\Models\User::where('id', $userId)->lockForUpdate()->firstOrFail();

                // 2. Comprobamos dentro de la transacción si ya tiene reserva activa
                $tieneActiva = Reservacion::where('user_id', $userId)
                    ->where('estado', 'Activa')
                    ->lockForUpdate()
                    ->exists();

                if ($tieneActiva) {
                    throw new Exception('Ya tienes una reservación activa. Cancélala o libérala antes de reservar otro equipo.');
                }

                // 3. Bloqueamos el equipo deseado
                $equipo = Equipo::where('id', $equipoId)->lockForUpdate()->firstOrFail();

                if ($equipo->estado !== 'Disponible') {
                    throw new Exception('Este equipo ya fue reservado por otro usuario o no está disponible.');
                }

                $equipo->estado = 'Ocupado';
                $equipo->save();

                Reservacion::create([
                    'user_id' => $userId,
                    'equipo_id' => $equipo->id,
                    'estado' => 'Activa',
                    'fecha_reserva' => now(),
                ]);
            });

            return redirect()->route('computo.index')->with('success', '¡Equipo reservado con éxito!');
        } catch (Exception $e) {
            return redirect()->route('computo.index')->with('error', $e->getMessage());
        }
    }

    // Cancelar / Liberar reservación con validación atómica dentro de la transacción
    public function cancelar($reservacionId)
    {
        $user = Auth::user();

        try {
            DB::transaction(function () use ($reservacionId, $user) {
                // Bloqueamos la reservación antes de leer su dueño o estado
                $reservacion = Reservacion::where('id', $reservacionId)->lockForUpdate()->firstOrFail();

                if ($user->rol !== 'admin' && $reservacion->user_id !== $user->id) {
                    abort(403, 'No tienes autorización para modificar reservaciones de otro usuario.');
                }

                if ($reservacion->estado !== 'Activa') {
                    throw new Exception('Esta reservación ya no se encuentra activa.');
                }

                $reservacion->estado = 'Cancelada';
                $reservacion->save();

                $equipo = Equipo::where('id', $reservacion->equipo_id)->lockForUpdate()->first();
                if ($equipo && $equipo->estado === 'Ocupado') {
                    $equipo->estado = 'Disponible';
                    $equipo->save();
                }
            });

            return redirect()->route('computo.index')->with('success', 'Reservación cancelada y equipo liberado.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function estadoEquipos()
    {
        $equipos = Equipo::select('id', 'estado')->get();

        return response()->json($equipos);
    }
}