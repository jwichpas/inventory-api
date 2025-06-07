<?php

namespace App\Models\Inventario;

use Illuminate\Database\Eloquent\Model;

class AtributosVariante extends Model
{
    protected $table = 'atributos_variante';
    protected $fillable = ['id_variante', 'id_atributo', 'value_id'];
    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}
