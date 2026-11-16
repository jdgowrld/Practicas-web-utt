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

    // Reservar un equipo con Transacción y Bloqueo de Fila (Anti-Concurrencia)
    public function reservar($equipoId)
    {
        $user = Auth::user();

        // Evitar que un estudiante acapare múltiples equipos activos a la vez
        $tieneActiva = Reservacion::where('user_id', $user->id)
            ->where('estado', 'Activa')
            ->exists();

        if ($tieneActiva) {
            return back()->with('error', 'Ya tienes una reservación activa. Cancélala o libérala antes de reservar otro equipo.');
        }

        try {
            DB::transaction(function () use ($equipoId, $user) {
                // BLOQUEO PESIMISTA: Si dos usuarios dan clic al mismo milisegundo,
                // MariaDB frena al segundo hasta que el primero termine esta transacción.
                $equipo = Equipo::where('id', $equipoId)->lockForUpdate()->firstOrFail();

                if ($equipo->estado !== 'Disponible') {
                    throw new Exception('Este equipo ya fue reservado por otro usuario o no está disponible.');
                }

                $equipo->estado = 'Ocupado';
                $equipo->save();

                Reservacion::create([
                    'user_id' => $user->id,
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

    // Cancelar / Liberar reservación (Con validación estricta de dueño)
    public function cancelar(Reservacion $reservacion)
    {
        $user = Auth::user();

        // CANDADO DE PRIVACIDAD: Un estudiante jamás puede cancelar la reserva de otro aunque altere la URL
        if ($user->rol !== 'admin' && $reservacion->user_id !== $user->id) {
            abort(403, 'No tienes autorización para modificar reservaciones de otro usuario.');
        }

        if ($reservacion->estado !== 'Activa') {
            return back()->with('error', 'Esta reservación ya no se encuentra activa.');
        }

        DB::transaction(function () use ($reservacion) {
            $reservacion->estado = 'Cancelada';
            $reservacion->save();

            $equipo = Equipo::where('id', $reservacion->equipo_id)->lockForUpdate()->first();
            if ($equipo && $equipo->estado === 'Ocupado') {
                $equipo->estado = 'Disponible';
                $equipo->save();
            }
        });

        return redirect()->route('computo.index')->with('success', 'Reservación cancelada y equipo liberado.');
    }
}