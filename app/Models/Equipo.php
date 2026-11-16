<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['codigo', 'descripcion', 'estado'])]
class Equipo extends Model
{
    public function reservaciones()
    {
        return $this->hasMany(Reservacion::class);
    }
}