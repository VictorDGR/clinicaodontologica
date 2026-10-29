<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

/** Clase abstracta Persona del diagrama: id, nombre, apellido, ci, telefono. */
abstract class Persona extends Model
{
    protected function nombreCompleto(): Attribute
    {
        return Attribute::get(fn (): string => "{$this->nombre} {$this->apellido}");
    }
}
