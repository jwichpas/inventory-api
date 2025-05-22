<?php

namespace App\Models\Empresa;

use Illuminate\Database\Eloquent\Model;

class EmpresaCondicionesSunat extends Model
{
    protected $table = 'empresa_condiciones_sunat';

    protected $fillable = [
        'empresa_id', 'periodo',  'cod_dom_habido', 'cod_estado', 'fec_alta',
        'cod_doble', 'cod_mclase', 'cod_reacti'
    ];
}
