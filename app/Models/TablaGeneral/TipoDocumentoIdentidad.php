<?php

namespace App\Models\TablaGeneral;

use Illuminate\Database\Eloquent\Model;

class TipoDocumentoIdentidad extends Model
{
    protected $table = 'tipo_documento_identidad';
    protected $primaryKey = 'id';

    /* public $timestamps = false; */
    protected $fillable = [
        'codigo',
        'name',
    ];
}
