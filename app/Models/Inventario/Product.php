<?php

namespace App\Models\Inventario;

use Illuminate\Database\Eloquent\Model;
use App\Models\Empresa;
use App\Models\Inventario\VarianteProduct;
use App\Models\Inventario\Brand;
use App\Models\Inventario\Categorie;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    protected $table = 'products';
    protected $fillable = ['id_empresa', 'id_brand', 'codigo', 'name', 'description', 'imagen'];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'id_empresa');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Categorie::class,'id_category');
    }
    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class,'id_brand');
    }


}
