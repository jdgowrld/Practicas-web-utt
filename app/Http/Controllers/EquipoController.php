<?php

namespace App\Http\Controllers;

use App\Models\Equipo;
use App\Models\Reservacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class EquipoController extends Controller
{
    public function create()
    {
        return view('computo.equipos.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'codigo' => 'required|string|max:50|unique:equipos,codigo',
            'descripcion' => 'required|string|max:255',
            'estado' => ['required', Rule::in(['Disponible', 'Mantenimiento'])],
        ], [
            'codigo.required' => 'El código del equipo es obligatorio.',
            'codigo.unique' => 'Este código de equipo ya está registrado.',
            'descripcion.required' => 'La descripción es obligatoria.',
        ]);

        Equipo::create($validated);

        return redirect()->route('computo.index')->with('success', 'Equipo registrado en el laboratorio.');
    }

    public function update(Request $request, Equipo $equipo)
    {
        $validated = $request->validate([
            'codigo' => ['required', 'string', 'max:50', Rule::unique('equipos', 'codigo')->ignore($equipo->id)],
            'descripcion' => 'required|string|max:255',
            'estado' => ['required', Rule::in(['Disponible', 'Ocupado', 'Mantenimiento'])],
        ], [
            'codigo.required' => 'El código es obligatorio.',
            'codigo.unique' => 'Este código ya pertenece a otro equipo.',
            'descripcion.required' => 'La descripción es obligatoria.',
        ]);

        DB::transaction(function () use ($equipo, $validated) {
            // Si el Admin pasa el equipo a Mantenimiento o lo libera manualmente a Disponible,
            // cancelamos automáticamente cualquier reservación activa que tuviera ese equipo.
            if (in_array($validated['estado'], ['Mantenimiento', 'Disponible'])) {
                Reservacion::where('equipo_id', $equipo->id)
                    ->where('estado', 'Activa')
                    ->update(['estado' => 'Cancelada']);
            }

            $equipo->update($validated);
        });

        return redirect()->route('computo.index')->with('success', 'Equipo actualizado y reservaciones sincronizadas.');
    }

    public function edit(Equipo $equipo)
    {
        return view('computo.equipos.edit', compact('equipo'));
    }

    public function destroy(Equipo $equipo)
    {
        if ($equipo->estado === 'Ocupado') {
            return back()->with('error', 'No puedes eliminar un equipo que tiene una reservación activa.');
        }

        $equipo->delete();

        return redirect()->route('computo.index')->with('success', 'Equipo eliminado del sistema.');
    }
}