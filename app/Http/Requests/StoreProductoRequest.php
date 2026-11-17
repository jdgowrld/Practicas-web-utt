<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StoreProductoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check() && Auth::user()->rol === 'admin';
    }

    public function rules(): array
    {
        return [
            'nombre' => 'required|string|max:255|unique:productos,nombre',
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
            'nombre.unique' => 'Ya existe un producto registrado con este nombre.',
            'descripcion.required' => 'La descripción es obligatoria.',
            'categoria_id.required' => 'Selecciona una categoría del catálogo.',
            'categoria_id.exists' => 'La categoría seleccionada no es válida.',
            'precio.required' => 'El precio es obligatorio.',
            'precio.numeric' => 'El precio debe ser un valor numérico.',
            'precio.gt' => 'El precio debe ser mayor a cero.',
            'existencias.required' => 'Las existencias son obligatorias.',
            'existencias.integer' => 'Las existencias deben ser un número entero.',
            'existencias.min' => 'Las existencias no pueden ser negativas.',
            'estado.required' => 'Selecciona el estado del producto.',
            'estado.in' => 'El estado debe ser Disponible o Agotado.',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($this->input('estado') === 'Disponible' && (int) $this->input('existencias') <= 0) {
                $validator->errors()->add('estado', 'No puedes marcar el producto como Disponible si tiene 0 existencias.');
            }
        });
    }

}