<?php

namespace App\Models\Inventario;

use Illuminate\Database\Eloquent\Model;
use App\Models\Empresa;

class UnidadMedida extends Model
{
    protected $table = 'unidad_medidas';
    protected $fillable = ['codigo_sunat', 'nombre_sunat', 'simbolo'];

}
