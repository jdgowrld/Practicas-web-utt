<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    // Listado de usuarios activos (y papelera si es Admin)
    public function index()
    {
        $usuariosActivos = User::orderBy('id', 'desc')->get();
        
        // Solo el admin consulta los eliminados lógicamente
        $usuariosEliminados = Auth::user()->rol === 'admin' 
            ? User::onlyTrashed()->orderBy('deleted_at', 'desc')->get() 
            : collect();

        return view('usuarios.index', compact('usuariosActivos', 'usuariosEliminados'));
    }

    // Formulario de creación
    public function create()
    {
        return view('usuarios.create');
    }

    // Guardar nuevo usuario
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'rol' => ['required', Rule::in(['admin', 'empleado', 'profesor', 'alumno', 'estudiante', 'jugador'])],
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'email.required' => 'El correo es obligatorio.',
            'email.email' => 'Ingresa un correo válido.',
            'email.unique' => 'Este correo ya está registrado.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'Mínimo 8 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'rol.required' => 'Selecciona un rol válido.',
            'rol.in' => 'El rol seleccionado no es válido.',
        ]);

        // Seguridad: Un empleado no puede crear usuarios con rol admin
        if (Auth::user()->rol !== 'admin' && $request->rol === 'admin') {
            abort(403, 'No tienes permisos para asignar el rol de Administrador.');
        }

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'rol' => $request->rol,
        ]);

        return redirect()->route('usuarios.index')->with('success', 'Usuario registrado correctamente.');
    }

    // Ver detalles de un usuario
    public function show(User $usuario)
    {
        return view('usuarios.show', compact('usuario'));
    }

    public function edit(User $usuario)
    {
        // CANDADO DE JERARQUÍA
        if (Auth::user()->rol !== 'admin' && $usuario->rol === 'admin') {
            abort(403, 'No tienes permisos para editar la cuenta de un Administrador.');
        }

        return view('usuarios.edit', compact('usuario'));
    }

    // Actualizar usuario
    public function update(Request $request, User $usuario)
    {
        // Candado 1: Un empleado jamás toca a un Admin
        if (Auth::user()->rol !== 'admin' && $usuario->rol === 'admin') {
            abort(403, 'No tienes permisos para modificar a un Administrador.');
        }

        // Reglas base para todos
        $rules = [
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($usuario->id)],
            'password' => 'nullable|string|min:8|confirmed',
        ];

        // Solo exigimos y validamos el rol si el usuario logueado es Admin
        if (Auth::user()->rol === 'admin') {
            $rules['rol'] = ['required', Rule::in(['admin', 'empleado', 'profesor', 'alumno', 'estudiante', 'jugador'])];
        }

        $request->validate($rules, [
            'name.required' => 'El nombre es obligatorio.',
            'email.required' => 'El correo es obligatorio.',
            'email.unique' => 'Este correo ya pertenece a otro usuario.',
            'password.min' => 'Mínimo 8 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'rol.required' => 'Selecciona un rol válido.',
        ]);

        $usuario->name = $request->name;
        $usuario->email = $request->email;

        // Candado 2: Solo el Admin puede cambiar el rol en la base de datos
        if (Auth::user()->rol === 'admin') {
            $usuario->rol = $request->rol;
        }

        if ($request->filled('password')) {
            $usuario->password = Hash::make($request->password);
        }

        $usuario->save();

        return redirect()->route('usuarios.index')->with('success', 'Información del usuario actualizada.');
    }

    
    // OPERACIONES  DE ADMIN

    // 1. Eliminación Lógica (Soft Delete)
    public function destroy(User $usuario)
    {
        if (Auth::user()->rol !== 'admin') {
            abort(403, 'Acción no autorizada.');
        }

        if ($usuario->id === Auth::id()) {
            return back()->with('error', 'No puedes deshabilitar tu propia cuenta activa.');
        }

        $usuario->delete();

        return redirect()->route('usuarios.index')->with('success', 'Usuario deshabilitado (Soft Delete).');
    }

    // 2. Restaurar usuario desde Soft Delete
    public function restore($id)
    {
        if (Auth::user()->rol !== 'admin') {
            abort(403, 'Acción no autorizada.');
        }

        $usuario = User::onlyTrashed()->findOrFail($id);
        $usuario->restore();

        return redirect()->route('usuarios.index')->with('success', 'Usuario restaurado correctamente.');
    }

    // 3. Eliminación Permanente 
    public function forceDelete($id)
    {
        if (Auth::user()->rol !== 'admin') {
            abort(403, 'Acción no autorizada.');
        }

        $usuario = User::withTrashed()->findOrFail($id);

        if ($usuario->id === Auth::id()) {
            return back()->with('error', 'No puedes eliminar permanentemente tu propia cuenta.');
        }

        $usuario->forceDelete();

        return redirect()->route('usuarios.index')->with('success', 'Usuario eliminado definitivamente de la base de datos.');
    }
}