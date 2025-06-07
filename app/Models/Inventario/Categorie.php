<?php

namespace App\Models\Inventario;

use Illuminate\Database\Eloquent\Model;
use App\Models\Empresa;
use App\Models\Inventario\Product;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Categorie extends Model
{
    protected $table = 'categories';
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
