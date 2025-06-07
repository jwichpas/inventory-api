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
    protected $fillable = [
        'nombre',
        'ruc',
        'direccion',
        'telefono',
        'correo',
        'usuario_sol',
        'clave_sol',
        'cliente_id',
        'cliente_secret',
        'token',
        'usuario_afp',
        'clave_afp',
        'imagen',
        'estado',
        'regimen_tributario',
        'regimen_t_desde',
        'regimen_laboral',
        'regimen_l_desde',
        'sunarp_oficina',
        'sunarp_partida',
        'sunarp_dni_representante',
        'sunarp_nombre_representante',
        'sunarp_cargo_representante'
    ];
    protected $hidden = [
        'clave_sol',
        'cliente_secret',
        'token',
        'clave_afp'
    ];

    public function condiciones(): HasOne
    {
        return $this->hasOne(EmpresaCondicionesSunat::class);
    }
    public function tributos(): HasMany
    {
        return $this->hasMany(EmpresaTributosSunat::class);
    }

    /**
     * Scope para filtrar empresas activas
     */
    public function scopeActivas($query)
    {
        return $query->where('estado', 1);
    }

    /**
     * Scope para buscar por nombre o RUC
     */
    public function scopeBuscar($query, $search)
    {
        return $query->where('nombre', 'like', "%{$search}%")
            ->orWhere('ruc', 'like', "%{$search}%");
    }

    public function usuarios()
    {
        return $this->belongsToMany(User::class, 'user_companies', 'empresa_id', 'user_id');
    }
}
