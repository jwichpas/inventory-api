<?php

namespace App\Models\Sire;

use Illuminate\Database\Eloquent\Model;
use App\Models\Sire\SireCompras;

class SireComprasTipoCambio extends Model
{
    protected $fillable = [
        'compra_id',
        'ind_carga_tipo_cambio',
        'mto_cambio_moneda_extranjera',
        'mto_cambio_moneda_dolares',
        'mto_tipo_cambio',
    ];
    public function compra()
    {
        return $this->belongsTo(SireCompras::class, 'id');
    }
}
