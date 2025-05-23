<?php

namespace App\Models\Sire;

use Illuminate\Database\Eloquent\Model;

class SireComprasArchivo extends Model
{
    protected $table = 'sire_compras_archivos';

    protected $fillable = [
        'compra_id',
        'xml',
        'cdr',
        'pdf',
        'guia',
    ];
}
