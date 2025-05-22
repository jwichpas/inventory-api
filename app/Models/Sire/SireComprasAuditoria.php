<?php

namespace App\Models\Sire;

use Illuminate\Database\Eloquent\Model;
use App\Models\Sire\SireCompras;

class SireComprasAuditoria extends Model
{
    protected $fillable = [
        'compra_id',
        'cod_usu_regis',
        'fec_regis',
        'cod_usu_modif',
        'fec_modif',
    ];
    public function compra()
    {
        return $this->belongsTo(SireCompras::class, 'id');
    }
}
