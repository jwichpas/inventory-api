<?php

namespace App\Models\Empresa;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class EmpresaTributosSunat extends Model
{
    protected $table = 'empresa_tributos_sunat';
    protected $fillable = [
        'empresa_id',
        'periodo',
        'cod_tributo',
        'fec_vigencia',
        'fec_alta',
        'cod_sis_pag',
        'cod_fre_pago',
        'cod_per_vsp',
        'mto_imp_min',
        'cod_ges_min',
        'ind_alta',
        'cod_tip_ins',
        'des_con_dis'
    ];

    protected $casts = [
        'fec_alta' => 'datetime',
        'fec_vigencia' => 'datetime'
    ];

    public function exoneracion(): HasOne
    {
        return $this->hasOne(EmpresaExoneracionesSunat::class, 'tributo_id');
    }
}
