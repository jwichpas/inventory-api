<?php

namespace App\Models\Inventario;

use Illuminate\Database\Eloquent\Model;
use App\Models\Empresa;
use App\Models\Inventario\Atributos;

class TipoAtributos extends Model
{
    protected $table = 'tipo_atributos';
    protected $fillable = ['id_empresa', 'name'];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'id_empresa');
    }

    public function atributos()
    {
        return $this->hasMany(Atributos::class, 'id_tipo');
    }
}
