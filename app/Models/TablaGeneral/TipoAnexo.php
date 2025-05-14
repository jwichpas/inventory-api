<?php

namespace App\Models\TablaGeneral;

use Illuminate\Database\Eloquent\Model;
use App\Models\Empresa;

class TipoAnexo extends Model
{
    protected $table = 'tipo_anexos';
    protected $fillable = ['id_empresa', 'codigo', 'name'];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'id_empresa');
    }
}
