<?php

namespace App\Models\TablaGeneral;

use Illuminate\Database\Eloquent\Model;

class TipoAfectacionIGV extends Model
{
    protected $table = 'tipo_afectacion_igv';
    protected $fillable = ['codigo', 'descripcion'];
    protected $primaryKey = 'codigo';
    public $incrementing = false;
    public $timestamps = false;
    protected $keyType = 'string';
}
