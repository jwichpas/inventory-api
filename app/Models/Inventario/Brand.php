<?php

namespace App\Models\Inventario;

use Illuminate\Database\Eloquent\Model;
use App\Models\Empresa;

class Brand extends Model
{
    protected $table = 'brands';
    protected $fillable = ['id_empresa', 'codigo', 'name', 'image'];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'id_empresa');
    }
}
