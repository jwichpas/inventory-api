<?php

namespace App\Models\Sire;

use Illuminate\Database\Eloquent\Model;

class SireResumenVentas extends Model
{
    protected $fillable = [
        'num_ruc',
        'anio',
        'mes',
        'total_ventas',
    ];

    /**
     * Los atributos que deben ser convertidos a tipos nativos.
     *
     * @var array
     */
    protected $casts = [
        'anio' => 'integer',
        'mes' => 'integer',
        'total_ventas' => 'decimal:2',
    ];
}
