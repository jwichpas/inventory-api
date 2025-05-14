<?php

namespace App\Models\Inventario;

use Illuminate\Database\Eloquent\Model;
use App\Models\Empresa;
class Almacen extends Model
{
    protected $table = 'almacenes';
    protected $fillable = ['id_empresa', 'nombre', 'pais', 'ciudad', 'direccion'];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'id_empresa');
    }

    public function stocks()
    {
        return $this->hasMany(AlmacenStock::class, 'id_almacen');
    }
}
