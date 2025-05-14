<?php

namespace App\Models\Inventario;

use Illuminate\Database\Eloquent\Model;

class AlmacenStock extends Model
{
    protected $table = 'almacen_stock';
    protected $fillable = ['id_variante', 'id_almacen', 'id_lote', 'stock'];

    public function variante()
    {
        return $this->belongsTo(VarianteProduct::class, 'id_variante');
    }

    public function almacen()
    {
        return $this->belongsTo(Almacen::class, 'id_almacen');
    }

    public function lote()
    {
        return $this->belongsTo(Lote::class, 'id_lote');
    }
}
