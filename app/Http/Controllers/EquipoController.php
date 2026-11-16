<?php

namespace App\Http\Controllers;

use App\Models\Equipo;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EquipoController extends Controller
{
    public function create()
    {
        return view('computo.equipos.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'codigo' => 'required|string|max:50|unique:equipos,codigo',
            'descripcion' => 'required|string|max:255',
            'estado' => ['required', Rule::in(['Disponible', 'Mantenimiento'])],
        ], [
            'codigo.required' => 'El código del equipo es obligatorio.',
            'codigo.unique' => 'Este código de equipo ya está registrado.',
            'descripcion.required' => 'La descripción es obligatoria.',
        ]);

        Equipo::create($request->validated());

        return redirect()->route('computo.index')->with('success', 'Equipo registrado en el laboratorio.');
    }

    public function edit(Equipo $equipo)
    {
        return view('computo.equipos.edit', compact('equipo'));
    }

    public function update(Request $request, Equipo $equipo)
    {
        $request->validate([
            'codigo' => ['required', 'string', 'max:50', Rule::unique('equipos', 'codigo')->ignore($equipo->id)],
            'descripcion' => 'required|string|max:255',
            'estado' => ['required', Rule::in(['Disponible', 'Ocupado', 'Mantenimiento'])],
        ], [
            'codigo.required' => 'El código es obligatorio.',
            'codigo.unique' => 'Este código ya pertenece a otro equipo.',
            'descripcion.required' => 'La descripción es obligatoria.',
        ]);

        $equipo->update($request->validated());

        return redirect()->route('computo.index')->with('success', 'Equipo actualizado correctamente.');
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