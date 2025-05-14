<?php

namespace App\Models\Inventario;

use Illuminate\Database\Eloquent\Model;
use App\Models\Empresa;
use App\Models\TablaGeneral\TipoAfectacionIGV;
use App\Models\TablaGeneral\TipoPrecioUnitario;

class MovimientoDetalle extends Model
{
    protected $table = 'movimiento_detalle';
    protected $fillable = [
        'id_empresa', 'id_cabecera', 'secuencia', 'id_variante', 'id_lote',
        'cantidad', 'valor_unitario', 'precio_unitario', 'valor_total',
        'precio_total', 'id_tipo_precio_unitario', 'id_tipo_afectacion_igv',
        'valor_unitario_final'
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'id_empresa');
    }

    public function cabecera()
    {
        return $this->belongsTo(MovimientoCabecera::class, 'id_cabecera');
    }

    public function variante()
    {
        return $this->belongsTo(VarianteProduct::class, 'id_variante');
    }

    public function lote()
    {
        return $this->belongsTo(Lote::class, 'id_lote');
    }

    public function tipoPrecioUnitario()
    {
        return $this->belongsTo(TipoPrecioUnitario::class, 'id_tipo_precio_unitario');
    }

    public function tipoAfectacionIgv()
    {
        return $this->belongsTo(TipoAfectacionIgv::class, 'id_tipo_afectacion_igv');
    }
}
