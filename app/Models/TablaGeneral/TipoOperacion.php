<?php

namespace App\Models\TablaGeneral;

use Illuminate\Database\Eloquent\Model;

class TipoOperacion extends Model
{
    protected $table = 'tipo_operacion';
    protected $fillable = ['codigo', 'descripcion'];
    protected $primaryKey = 'codigo';
    public $incrementing = false;
    public $timestamps = false;
    protected $keyType = 'string';
}
