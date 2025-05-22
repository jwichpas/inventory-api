<?php

namespace App\Models\Sire;

use Illuminate\Database\Eloquent\Model;
use App\Models\Sire\SireVentasDocModificados;

class SireVentas extends Model
{
    /**
     * Los atributos que son asignables masivamente.
     *
     * @var array
     */
    protected $fillable = [
        'id_externo',
        'num_ruc',
        'nom_razon_social',
        'per_periodo_tributario',
        'cod_car',
        'cod_tipo_cdp',
        'num_serie_cdp',
        'num_cdp',
        'cod_tipo_carga',
        'cod_situacion',
        'fec_emision',
        'cod_tipo_doc_identidad',
        'num_doc_identidad',
        'nom_razon_social_cliente',
        'mto_val_fact_expo',
        'mto_bi_gravada',
        'mto_dscto_bi',
        'mto_igv',
        'mto_dscto_igv',
        'mto_exonerado',
        'mto_inafecto',
        'mto_isc',
        'mto_bi_ivap',
        'mto_ivap',
        'mto_icbp',
        'mto_otros_trib',
        'mto_total_cp',
        'cod_moneda',
        'mto_tipo_cambio',
        'cod_estado_comprobante',
        'des_estado_comprobante',
        'ind_oper_gratuita',
        'mto_valor_op_gratuitas',
        'mto_valor_fob',
        'ind_tipo_operacion',
        'mto_porc_participacion',
        'mto_valor_fob_dolar',
        'num_Inconsistencias',
        'semaforo',
        'lis_cod_Inconsistencia',
    ];

    /**
     * Obtener los documentos modificatorios asociados con esta venta.
     */
    public function documentoMods()
    {
        return $this->hasMany(SireVentasDocModificados::class);
    }
}
