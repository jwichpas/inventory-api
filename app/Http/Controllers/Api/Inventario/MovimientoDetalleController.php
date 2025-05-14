<?php

namespace App\Http\Controllers\Api\Inventario;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Inventario\MovimientoDetalle;
use Illuminate\Support\Facades\Validator;

class MovimientoDetalleController extends Controller
{
    public function index()
    {
        $detalles = MovimientoDetalle::with([
            'empresa',
            'cabecera',
            'variante',
            'lote',
            'tipoPrecioUnitario',
            'tipoAfectacionIgv'
        ])->get();
        return response()->json($detalles);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_empresa' => 'required|exists:empresas,id',
            'id_cabecera' => 'required|exists:movimiento_cabecera,id',
            'secuencia' => 'required|integer',
            'id_variante' => 'required|exists:variantes_producto,id',
            'id_lote' => 'nullable|exists:lotes,id',
            'cantidad' => 'required|numeric|min:0',
            'valor_unitario' => 'required|numeric|min:0',
            'precio_unitario' => 'required|numeric|min:0',
            'valor_total' => 'required|numeric|min:0',
            'precio_total' => 'required|numeric|min:0',
            'id_tipo_precio_unitario' => 'required|exists:tipo_precio_unitario_fe,codigo',
            'id_tipo_afectacion_igv' => 'required|exists:tipo_afectacion_igv,codigo',
            'valor_unitario_final' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $detalle = MovimientoDetalle::updateOrCreate(
            [
                'id_cabecera' => $request->id_cabecera,
                'secuencia' => $request->secuencia
            ],
            $request->all()
        );

        return response()->json($detalle->load([
            'empresa',
            'cabecera',
            'variante',
            'lote',
            'tipoPrecioUnitario',
            'tipoAfectacionIgv'
        ]), 201);
    }

    public function show($id)
    {
        $detalle = MovimientoDetalle::with([
            'empresa',
            'cabecera',
            'variante',
            'lote',
            'tipoPrecioUnitario',
            'tipoAfectacionIgv'
        ])->find($id);
        if (!$detalle) {
            return response()->json(['message' => 'Detalle de movimiento no encontrado'], 404);
        }
        return response()->json($detalle);
    }

    public function update(Request $request, $id)
    {
        $detalle = MovimientoDetalle::find($id);
        if (!$detalle) {
            return response()->json(['message' => 'Detalle de movimiento no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'id_empresa' => 'exists:empresas,id',
            'id_cabecera' => 'exists:movimiento_cabecera,id',
            'secuencia' => 'integer',
            'id_variante' => 'exists:variantes_producto,id',
            'id_lote' => 'nullable|exists:lotes,id',
            'cantidad' => 'numeric|min:0',
            'valor_unitario' => 'numeric|min:0',
            'precio_unitario' => 'numeric|min:0',
            'valor_total' => 'numeric|min:0',
            'precio_total' => 'numeric|min:0',
            'id_tipo_precio_unitario' => 'exists:tipo_precio_unitario_fe,codigo',
            'id_tipo_afectacion_igv' => 'exists:catalogofe_07_tipoafectacion_igv,codigo',
            'valor_unitario_final' => 'numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $detalle->update($request->all());
        return response()->json($detalle->load([
            'empresa',
            'cabecera',
            'variante',
            'lote',
            'tipoPrecioUnitario',
            'tipoAfectacionIgv'
        ]));
    }

    public function destroy($id)
    {
        $detalle = MovimientoDetalle::find($id);
        if (!$detalle) {
            return response()->json(['message' => 'Detalle de movimiento no encontrado'], 404);
        }

        $detalle->delete();
        return response()->json(['message' => 'Detalle de movimiento eliminado correctamente']);
    }
}
