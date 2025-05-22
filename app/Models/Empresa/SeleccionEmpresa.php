<?php

namespace App\Models\Empresa;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Empresa;
use App\Models\User;

class SeleccionEmpresa extends Model
{
    use HasFactory;

    protected $fillable = [
        'empresa_id',
    ];

    // Relación con el modelo Empresa
    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }
    // Relación con la tabla users (opcional)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
