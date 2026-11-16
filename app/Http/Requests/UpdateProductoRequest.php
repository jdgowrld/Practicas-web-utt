<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateProductoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check() && Auth::user()->rol === 'admin';
    }

    public function rules(): array
    {
        $productoId = $this->route('producto')->id;

        return [
            'nombre' => ['required', 'string', 'max:255', Rule::unique('productos', 'nombre')->ignore($productoId)],
            'descripcion' => 'required|string',
            'categoria_id' => 'required|exists:categorias,id',
            'precio' => 'required|numeric|gt:0',
            'existencias' => 'required|integer|min:0',
            'estado' => ['required', Rule::in(['Disponible', 'Agotado'])],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre del producto es obligatorio.',
            'nombre.unique' => 'Ya existe otro producto con este nombre.',
            'descripcion.required' => 'La descripción es obligatoria.',
            'categoria_id.required' => 'Selecciona una categoría del catálogo.',
            'precio.required' => 'El precio es obligatorio.',
            'precio.numeric' => 'El precio debe ser numérico.',
            'precio.gt' => 'El precio debe ser mayor a cero.',
            'existencias.required' => 'Las existencias son obligatorias.',
            'existencias.integer' => 'Debe ser un número entero.',
            'existencias.min' => 'No puede ser menor a cero.',
            'estado.required' => 'Selecciona el estado.',
            'estado.in' => 'Estado no válido.',
        ];
    }
}