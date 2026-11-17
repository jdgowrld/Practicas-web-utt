<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'jugador_x_id',
    'jugador_o_id',
    'turno_user_id',
    'ganador_id',
    'tablero',
    'estado',
    'es_empate',
])]
class Partida extends Model
{
    protected function casts(): array
    {
        return [
            'tablero' => 'array',
            'es_empate' => 'boolean',
        ];
    }

    public function jugadorX()
    {
        return $this->belongsTo(User::class, 'jugador_x_id');
    }

    public function jugadorO()
    {
        return $this->belongsTo(User::class, 'jugador_o_id');
    }

    public function turnoActual()
    {
        return $this->belongsTo(User::class, 'turno_user_id');
    }

    public function ganador()
    {
        return $this->belongsTo(User::class, 'ganador_id');
    }
}