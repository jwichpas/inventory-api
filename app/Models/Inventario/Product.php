<?php

namespace App\Models\Inventario;

use Illuminate\Database\Eloquent\Model;
use App\Models\Empresa;
use App\Models\Inventario\VarianteProduct;
use App\Models\Inventario\Brand;
use App\Models\Inventario\Categorie;

class Product extends Model
{
    protected $table = 'products';
    protected $fillable = ['id_empresa', 'id_brand', 'codigo', 'name', 'description', 'imagen'];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'id_empresa');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'id_brand');
    }

    public function variantes()
    {
        return $this->hasMany(VarianteProduct::class, 'id_producto');
    }

    public function categorias()
    {
        return $this->belongsToMany(Categorie::class, 'producto_categoria', 'id_producto', 'id_categoria');
    }
}
