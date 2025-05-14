<?php

namespace App\Models\Inventario;

use Illuminate\Database\Eloquent\Model;

class UnidadMedida extends Model
{
    protected $table = 'unidad_medida';
    protected $fillable = ['codigo_sunat', 'nombre_sunat', 'simbolo'];
}
