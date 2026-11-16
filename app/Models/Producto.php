<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['nombre', 'descripcion', 'categoria_id', 'precio', 'existencias', 'estado'])]
class Producto extends Model
{
    use HasFactory;

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    // Scope para combinar todos los filtros dinámicamente
    public function scopeFiltrar(Builder $query, array $filtros): Builder
    {
        return $query
            ->when($filtros['buscar'] ?? null, function ($q, $buscar) {
                $q->where('nombre', 'like', "%{$buscar}%");
            })
            ->when($filtros['categoria_id'] ?? null, function ($q, $categoriaId) {
                $q->where('categoria_id', $categoriaId);
            })
            ->when(isset($filtros['precio_min']) && $filtros['precio_min'] !== '', function ($q) use ($filtros) {
                $q->where('precio', '>=', (float) $filtros['precio_min']);
            })
            ->when(isset($filtros['precio_max']) && $filtros['precio_max'] !== '', function ($q) use ($filtros) {
                $q->where('precio', '<=', (float) $filtros['precio_max']);
            })
            ->when($filtros['estado'] ?? null, function ($q, $estado) {
                $q->where('estado', $estado);
            });
    }
}