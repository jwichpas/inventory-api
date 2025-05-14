<?php

namespace App\Models\TablaGeneral;

use Illuminate\Database\Eloquent\Model;

class TipoOperacionPle extends Model
{
    protected $table = 'tipo_operacion_ple';
    protected $fillable = ['codigo', 'descripcion'];
    protected $primaryKey = 'codigo';
    public $incrementing = false;
    public $timestamps = false;
    public $keyType = 'string';
}
