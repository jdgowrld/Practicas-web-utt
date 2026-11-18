<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['user_id', 'equipo_id', 'estado', 'fecha_reserva'])]
class Reservacion extends Model
{
    protected function casts(): array
    {
        return [
            'fecha_reserva' => 'datetime',
        ];
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id')->withTrashed();
    }

    public function equipo()
    {
        return $this->belongsTo(Equipo::class);
    }
}