<?php

namespace App\Models\Sire;

use Illuminate\Database\Eloquent\Model;

class SireVentasArchivo extends Model
{
    protected $table = 'sire_ventas_archivos';

    protected $fillable = [
        'venta_id',
        'xml',
        'cdr',
        'pdf',
        'guia',
    ];
}
