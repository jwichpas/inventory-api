<?php

namespace App\Models\Inventario;

use Illuminate\Database\Eloquent\Model;

class VarianteProduct extends Model
{
    protected $table = 'variante_products';
    protected $fillable = [
        'id_producto',
        'sku',
        'codigo_sunat',
        'ean13',
        'ean14',
        'imagen',
        'costo',
        'precio',
        'id_unidad_medida'
    ];
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function producto()
    {
        return $this->belongsTo(Product::class, 'id_producto');
    }

    public function unidadMedida()
    {
        return $this->belongsTo(UnidadMedida::class, 'id_unidad_medida');
    }

    public function atributos()
    {
        return $this->belongsToMany(Atributos::class, 'atributos_variante', 'id_variante', 'id_atributo')->withPivot('id_variante', 'id_atributo');
    }

    public function lotes()
    {
        return $this->hasMany(Lote::class, 'id_variante');
    }
    public function attributes()
    {
        return $this->belongsToMany(AtributosVariante::class, 'atributos_variante', 'id_variante', 'id_atributo');
    }

    public function stocks()
    {
        return $this->hasMany(AlmacenStock::class, 'id_variante');
    }
}
