<?php

namespace App\Models\Inventario;

use Illuminate\Database\Eloquent\Model;
use App\Models\Empresa;
use App\Models\Inventario\Product;

class Categorie extends Model
{
    protected $table = 'categories';
    protected $fillable = ['id_empresa', 'codigo', 'name', 'image'];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'id_empresa');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'producto_categoria', 'id_categoria', 'id_producto');
    }
}
