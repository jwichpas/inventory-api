<?php

namespace App\Models\Sire;

use Illuminate\Database\Eloquent\Model;
use App\Models\Sire\SireCompras;
class SireComprasClasificacion extends Model
{
    protected $table = 'sire_compras_clasificacions';

    protected $fillable = [
        'compra_id',
        'tipo_proveedor',
        'estado'
    ];

    protected $casts = [
        'tipo_proveedor' => 'string',
        'estado' => 'string'
    ];

    // Relación con la compra
    public function compra()
    {
        return $this->belongsTo(SireCompras::class, 'compra_id', 'id');
    }
}
