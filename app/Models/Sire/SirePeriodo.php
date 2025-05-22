<?php

namespace App\Models\Sire;

use Illuminate\Database\Eloquent\Model;
use App\Models\Sire\SireEjercicio;

class SirePeriodo extends Model
{
    protected $fillable = [
        'ejercicios_id',
        'per_tributario',
        'cod_estado',
        'des_estado',
    ];

    // Relación muchos a uno con la tabla ejercicios
    public function sire_ejercicio()
    {
        return $this->belongsTo(SireEjercicio::class, 'ejercicios_id');
    }
}
