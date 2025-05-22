<?php

namespace App\Models\Sire;

use Illuminate\Database\Eloquent\Model;
use App\Models\Empresa;
use App\Models\Sire\SirePeriodo;

class SireEjercicio extends Model
{
    protected $fillable = [
        'empresa_id',
        'num_ejercicio',
        'des_estado',
    ];

    // Relación uno a muchos con la tabla periodos
    public function sire_periodos()
    {
        return $this->hasMany(SirePeriodo::class, 'ejercicios_id');
    }
    // Relación muchos a uno con la tabla empresas
    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }
}
