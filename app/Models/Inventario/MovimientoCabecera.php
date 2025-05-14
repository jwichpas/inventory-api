<?php

namespace App\Models\Inventario;

use Illuminate\Database\Eloquent\Model;
use App\Models\Empresa;
use App\Models\TablaGeneral\Anexo;
use App\Models\TablaGeneral\TipoDocumento;
use App\Models\TablaGeneral\TipoOperacionPle;
use App\Models\TablaGeneral\TipoOperacion;
use App\Models\Inventario\MovimientoDetalle;

class MovimientoCabecera extends Model
{
    protected $table = 'movimiento_cabecera';
    protected $fillable = [
        'id_empresa',
        'fecha_emision',
        'fecha_vencimiento',
        'codigo_anexo',
        'id_proveedor',
        'id_tipo_invoice',
        'serie',
        'numero',
        'moneda',
        'tipo_cambio',
        'valor_compra',
        'gratuito',
        'igv',
        'total',
        'total_moneda_base',
        'id_tipo_operacion',
        'id_tipo_operacion_fe',
        'periodo',
        'estado',
        'fecha_recepcion',
        'tipo_movimiento',
        'flete',
        'forma_pago'
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'id_empresa');
    }

    public function proveedor()
    {
        return $this->belongsTo(Anexo::class, 'id_proveedor');
    }

    public function tipoDocumento()
    {
        return $this->belongsTo(TipoDocumento::class, 'id_tipo_invoice');
    }

    public function tipoOperacionPle()
    {
        return $this->belongsTo(TipoOperacionPle::class, 'id_tipo_operacion');
    }

    public function tipoOperacionFe()
    {
        return $this->belongsTo(TipoOperacion::class, 'id_tipo_operacion_fe');
    }

    public function detalles()
    {
        return $this->hasMany(MovimientoDetalle::class, 'id_cabecera');
    }
}
