<?php

namespace App\Models\Inventario;

use Illuminate\Database\Eloquent\Model;

class Lote extends Model
{
    protected $table = 'lotes';
    protected $fillable = ['id_variante', 'codigo_lote', 'fecha_vencimiento', 'fecha_fabricacion'];

    public function variante()
    {
        return $this->belongsTo(VarianteProduct::class, 'id_variante');
    }

    public function stocks()
    {
        return $this->hasMany(AlmacenStock::class, 'id_lote');
    }
}
