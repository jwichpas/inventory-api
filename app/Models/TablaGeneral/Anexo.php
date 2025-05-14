<?php

namespace App\Models\TablaGeneral;

use Illuminate\Database\Eloquent\Model;
use App\Models\Empresa;

class Anexo extends Model
{
    protected $table = 'anexos';
    protected $fillable = ['id_empresa', 'tipo_anexo', 'codigo', 'documento', 'nombre', 'direccion', 'telefono', 'email', 'estado'];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'id_empresa');
    }
}
