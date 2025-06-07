<?php

namespace App\Models\Inventario;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Empresa;
use App\Models\Inventario\Product;

class Brand extends Model
{
    protected $table = 'brands';
    protected $fillable = ['id_empresa', 'codigo', 'name', 'image'];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'id_empresa');
    }
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
