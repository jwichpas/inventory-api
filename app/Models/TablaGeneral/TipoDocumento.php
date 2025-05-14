<?php

namespace App\Models\TablaGeneral;

use Illuminate\Database\Eloquent\Model;

class TipoDocumento extends Model
{
    protected $table = 'tipo_documento';
    protected $fillable = ['codigo', 'descripcion', 'simbolo'];
    protected $primaryKey = 'codigo';
    public $incrementing = false;
    public $timestamps = false;
    public $keyType = 'string';
}
