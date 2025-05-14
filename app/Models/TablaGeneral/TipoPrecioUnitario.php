<?php

namespace App\Models\TablaGeneral;

use Illuminate\Database\Eloquent\Model;

class TipoPrecioUnitario extends Model
{
    protected $table = 'tipo_precio_unitario_fe';
    protected $fillable = ['codigo', 'descripcion'];
    protected $primaryKey = 'codigo';
    public $incrementing = false;
    public $timestamps = false;
    protected $keyType = 'string';
}
