<?php

namespace App\Models\Sire;

use Illuminate\Database\Eloquent\Model;
use App\Models\Sire\SireCompras;

class SireComprasDocModificados extends Model
{
    protected $fillable = [
        'compra_id',
        'cod_documento',
        'num_serie',
        'num_documento',
        'fec_emision_mod',
    ];
    public function compra()
    {
        return $this->belongsTo(SireCompras::class, 'id');
    }
}
