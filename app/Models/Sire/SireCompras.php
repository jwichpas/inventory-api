<?php

namespace App\Models\Sire;

use Illuminate\Database\Eloquent\Model;
use App\Models\Sire\SireComprasTipoCambio;
use App\Models\Sire\SireComprasMonto;
use App\Models\Sire\SireComprasDocModificados;
use App\Models\Sire\SireComprasAuditoria;

class SireCompras extends Model
{
    protected $fillable = [

        'id_registro',
        'num_ruc',
        'nom_razon_social',
        'cod_car',
        'cod_tipo_cdp',
        'des_tipo_cdp',
        'num_serie_cdp',
        'num_cdp',
        'fec_emision',
        'fec_venc_pag',
        'num_cdp_rango_final',
        'cod_tipo_doc_identidad_proveedor',
        'num_doc_identidad_proveedor',
        'nom_razon_social_proveedor',
        'cod_tipo_carga',
        'cod_situacion',
        'cod_moneda',
        'mto_total_cp',
        'cod_estado_comprobante',
        'des_estado_comprobante',
        'ind_oper_gratuita',
        'cod_tipo_motivo_nota',
        'des_tipo_motivo_nota',
        'ind_editable',
        'per_tributario',
        'num_inconsistencias',
        'ind_inf_incompleta',
        'ind_modificado_contribuyente',
        'plazo_visualizacion',
        'ind_detraccion',
        'ind_inclu_exclu_car',
        'por_participacion',
        'cod_bbss',
        'cod_id_proyecto',
        'ann_cdp',
        'cod_dep_aduanera',
        'ind_fuente_cp',
        'lis_cod_inconsistencia',
        'lis_num_casilla',
        'por_tasa_retencion',
        'des_msj_original',
        'num_car_ind_ie',
        'num_correlativo',
        'por_tasa_igv',
        'archivo_carga',
        'campos_libres',
    ];
    protected $casts = [
        'lis_cod_inconsistencia' => 'array', // Manejar como array automáticamente
        'lis_num_casilla' => 'array',       // Manejar como array automáticamente
    ];
    // Relación con TipoCambio
    public function tipoCambio()
    {
        return $this->hasOne(SireComprasTipoCambio::class, 'compra_id');
    }

    // Relación con Monto
    public function montos()
    {
        return $this->hasOne(SireComprasMonto::class, 'compra_id');
    }

    // Relación con DocumentosModificados
    public function documentosModificados()
    {
        return $this->hasMany(SireComprasDocModificados::class, 'compra_id');
    }

    // Relación con Auditoria
    public function auditoria()
    {
        return $this->hasOne(SireComprasAuditoria::class, 'compra_id');
    }
    public function archivo()
    {
        return $this->hasMany(SireComprasArchivo::class, 'compra_id');
    }
}
