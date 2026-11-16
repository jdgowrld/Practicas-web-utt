<?php

namespace App\Http\Controllers;

use App\Models\Calificacion;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class CalificacionController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // ROL ALUMNO: Solo consulta su propio registro (candado de privacidad)
        if ($user->rol === 'alumno') {
            $miCalificacion = Calificacion::with('alumno')->where('user_id', $user->id)->first();
            return view('academico.alumno', compact('miCalificacion'));
        }

        // ROL PROFESOR (y Admin): Listado con ordenamiento dinámico
        $orden = $request->get('orden', 'nombre_asc');

        $query = Calificacion::with('alumno')
            ->join('users', 'calificacions.user_id', '=', 'users.id')
            ->select('calificacions.*');

        switch ($orden) {
            case 'nombre_desc':
                $query->orderBy('users.name', 'desc');
                break;
            case 'calificacion_desc':
                $query->orderBy('calificacions.calificacion', 'desc');
                break;
            case 'calificacion_asc':
                $query->orderBy('calificacions.calificacion', 'asc');
                break;
            case 'faltas_desc':
                $query->orderBy('calificacions.faltas', 'desc');
                break;
            case 'faltas_asc':
                $query->orderBy('calificacions.faltas', 'asc');
                break;
            default: // nombre_asc
                $query->orderBy('users.name', 'asc');
                break;
        }

        $registros = $query->get();

        return view('academico.index', compact('registros', 'orden'));
    }

    public function create()
    {
        // Solo listamos usuarios con rol 'alumno' que aún no tengan calificación asignada
        $alumnosDisponibles = User::where('rol', 'alumno')
            ->whereDoesntHave('calificacion') // Agregaremos esta relación en User.php
            ->orderBy('name')
            ->get();

        return view('academico.create', compact('alumnosDisponibles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => [
                'required',
                Rule::exists('users', 'id')->where('rol', 'alumno'),
                'unique:calificacions,user_id'
            ],
            'faltas' => 'required|integer|min:0|max:50',
            'calificacion' => 'required|numeric|min:0|max:10',
        ], [
            'user_id.required' => 'Selecciona un alumno.',
            'user_id.exists' => 'El usuario seleccionado no tiene rol de Alumno.',
            'user_id.unique' => 'Este alumno ya tiene una calificación registrada.',
            'faltas.required' => 'Ingresa el número de faltas.',
            'faltas.integer' => 'Las faltas deben ser un número entero.',
            'faltas.min' => 'Las faltas no pueden ser negativas.',
            'faltas.max' => 'El máximo permitido es 50 faltas.',
            'calificacion.required' => 'Ingresa la calificación numérica.',
            'calificacion.numeric' => 'La calificación debe ser un número.',
            'calificacion.min' => 'La calificación mínima es 0.',
            'calificacion.max' => 'La calificación máxima es 10.',
        ]);

        Calificacion::create($request->only(['user_id', 'faltas', 'calificacion']));

        return redirect()->route('academico.index')->with('success', 'Registro académico guardado correctamente.');
    }

    public function edit(Calificacion $academico)
    {
        $academico->load('alumno');
        return view('academico.edit', compact('academico'));
    }

    public function update(Request $request, Calificacion $academico)
    {
        $request->validate([
            'faltas' => 'required|integer|min:0|max:50',
            'calificacion' => 'required|numeric|min:0|max:10',
        ], [
            'faltas.required' => 'Ingresa el número de faltas.',
            'faltas.integer' => 'Debe ser un número entero.',
            'faltas.min' => 'No puede ser menor a 0.',
            'faltas.max' => 'Máximo 50 faltas.',
            'calificacion.required' => 'Ingresa la calificación.',
            'calificacion.numeric' => 'Debe ser numérico.',
            'calificacion.min' => 'Mínimo 0.',
            'calificacion.max' => 'Máximo 10.',
        ]);

        $academico->update($request->only(['faltas', 'calificacion']));

        return redirect()->route('academico.index')->with('success', 'Calificación y faltas actualizadas.');
    }

    public function destroy(Calificacion $academico)
    {
        $academico->delete();
        return redirect()->route('academico.index')->with('success', 'Registro académico eliminado.');
    }
}