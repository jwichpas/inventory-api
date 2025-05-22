<?php

namespace App\Models\Sire;

use Illuminate\Database\Eloquent\Model;
use App\Models\Sire\SireCompras;

class SireComprasMonto extends Model
{
    protected $fillable = [
        'compra_id',               // idRegistro
        'mto_bi_gravada_dg',          // mtoBIGravadaDG
        'mto_igv_ipm_dg',             // mtoIgvIpmDG
        'mto_bi_gravada_dgng',        // mtoBIGravadaDGNG
        'mto_igv_ipm_dgng',           // mtoIgvIpmDGNG
        'mto_bi_gravada_dng',         // mtoBIGravadaDNG
        'mto_igv_ipm_dng',            // mtoIgvIpmDNG
        'mto_valor_adq_ng',           // mtoValorAdqNG
        'mto_icbp',                   // mtoIcbp
        'mto_otros_trib',             // mtoOtrosTrib
        'mto_total_cp',               // mtoTotalCp
        'mto_isc',                    // mtoISC
        'mto_imb',                    // mtoIMB
        'mto_bi_gravada_dg_original', // mtoBIGravadaDGOriginal
        'mto_igv_ipm_dg_original',    // mtoIgvIpmDGOriginal
    ];
    public function compra()
    {
        return $this->belongsTo(SireCompras::class, 'id');
    }
}
