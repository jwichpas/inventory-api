<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\Empresa\EmpresaCondicionesSunat;
use App\Models\Empresa\EmpresaTributosSunat;
use Illuminate\Database\Eloquent\Relations\HasMany;



class Empresa extends Model
{
    protected $table = 'empresas';
    protected $fillable = ['nombre', 'ruc', 'direccion'];

    public function condiciones(): HasOne
    {
        return $this->hasOne(EmpresaCondicionesSunat::class);
    }
    public function tributos(): HasMany
    {
        return $this->hasMany(EmpresaTributosSunat::class);
    }
}


