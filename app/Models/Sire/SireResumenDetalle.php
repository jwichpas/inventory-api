<?php

namespace App\Models\Sire;

use Illuminate\Database\Eloquent\Model;
use App\Models\Empresa;

class SireResumenDetalle extends Model
{
    protected $table = 'sire_resumen_detalles';
    protected $primaryKey = 'id';
    protected $fillable = [
        'numRuc',
        'correlativo',
        'nomRazonSocial',
        'perTributario',
        'nomRegistro',
        'constancia',
        'nomArchivoConstanciaComprasPdf',
        'nomArchivoConstanciaVentasPdf',
        'fechGeneracion',
        'fechVencimiento',
        'codEstadoGeneracion',
        'desEstadoGeneracion',
        'perTributarioFormateado'
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'numRuc', 'ruc');
    }
}
