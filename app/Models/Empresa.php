<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
<<<<<<< HEAD
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\Empresa\EmpresaCondicionesSunat;
use App\Models\Empresa\EmpresaTributosSunat;
use Illuminate\Database\Eloquent\Relations\HasMany;


=======
>>>>>>> develop

class Empresa extends Model
{
    protected $table = 'empresas';
    protected $fillable = ['nombre', 'ruc', 'direccion'];
<<<<<<< HEAD

    public function condiciones(): HasOne
    {
        return $this->hasOne(EmpresaCondicionesSunat::class);
    }
    public function tributos(): HasMany
    {
        return $this->hasMany(EmpresaTributosSunat::class);
    }
}


=======
}
>>>>>>> develop
