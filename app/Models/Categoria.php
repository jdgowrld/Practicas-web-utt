<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['nombre'])]
class Categoria extends Model
{
    public function productos()
    {
        return $this->hasMany(Producto::class);
    }
}