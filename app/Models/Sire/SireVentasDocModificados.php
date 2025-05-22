<?php

namespace App\Models\Sire;

use Illuminate\Database\Eloquent\Model;
use App\Models\Sire\SireVentas;

class SireVentasDocModificados extends Model
{
    /**
     * Los atributos que son asignables masivamente.
     *
     * @var array
     */
    protected $fillable = [
        'venta_id',
        'fec_emision_mod',
        'cod_tipo_cdp_mod',
        'num_serie_cdp_mod',
        'num_cdp_mod',
    ];

    /**
     * Obtener la venta asociada con este documento modificatorio.
     */
    public function venta()
    {
        return $this->belongsTo(SireVentas::class);
    }
}
