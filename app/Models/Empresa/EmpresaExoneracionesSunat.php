<?php

namespace App\Models\Empresa;

use Illuminate\Database\Eloquent\Model;

class EmpresaExoneracionesSunat extends Model
{
    protected $table = 'empresa_exoneraciones_sunat';
    protected $fillable = ['tributo_id', 'cod_exo_dis'];
}
