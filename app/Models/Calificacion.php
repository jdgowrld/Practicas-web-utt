<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['user_id', 'faltas', 'calificacion'])]
class Calificacion extends Model
{
    public function alumno()
    {
    return $this->belongsTo(User::class, 'user_id')->withTrashed();
    }

    // Accesor automático: $registro->equivalencia
    // Escala UTT: < 8.0 = NA | 8.0 - 8.4 = SA | 8.5 - 9.4 = DE | 9.5 - 10 = AU
    public function getEquivalenciaAttribute(): string
    {
        $nota = (float) $this->calificacion;

        if ($nota >= 9.5) return 'AU'; // Autónomo
        if ($nota >= 8.5) return 'DE'; // Destacado
        if ($nota >= 8.0) return 'SA'; // Suficiente
        
        return 'NA'; // No Acreditado
    }
}