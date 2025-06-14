<?php

namespace App\Models\Inventario;

use Illuminate\Database\Eloquent\Model;
use App\Models\Empresa;
use App\Models\Inventario\TipoAtributos;
use App\Models\Inventario\VarianteProduct;

class Atributos extends Model
{
    protected $table = 'atributos';
    protected $fillable = ['id_empresa', 'id_tipo', 'valor'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'id_empresa');
    }

    public function tipo()
    {
        return $this->belongsTo(TipoAtributos::class, 'id_tipo');
    }

    public function variantes()
    {
        return $this->belongsToMany(VarianteProduct::class, 'atributos_variante', 'id_atributo', 'id_variante');
    }

    public function tipoAtributo()
    {
        return $this->belongsTo(TipoAtributos::class, 'id_tipo');
    }

}
