<?php

namespace App\Http\Controllers\Api\Inventario;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Inventario\MovimientoCabecera;
use Illuminate\Support\Facades\Validator;

class MovimientoCabeceraController extends Controller
{
    public function index()
    {
        $movimientos = MovimientoCabecera::with([
            'empresa',
            'proveedor',
            'tipoDocumento',
            'tipoOperacionPle',
            'tipoOperacionFe',
            'detalles'
        ])->get();
        return response()->json($movimientos);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_empresa' => 'required|exists:empresas,id',
            'fecha_emision' => 'required|date',
            'fecha_vencimiento' => 'nullable|date',
            'codigo_anexo' => 'nullable|integer',
            'id_proveedor' => 'nullable|exists:anexos,id',
            'id_tipo_invoice' => 'required|exists:tipo_documento,codigo',
            'serie' => 'required|string|max:10',
            'numero' => 'required|string|max:20',
            'moneda' => 'required|string|max:3',
            'tipo_cambio' => 'required|numeric|min:0',
            'valor_compra' => 'required|numeric|min:0',
            'gratuito' => 'required|numeric|min:0',
            'igv' => 'required|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'total_moneda_base' => 'required|numeric|min:0',
            'id_tipo_operacion' => 'required|exists:tipo_operacion_ple,codigo',
            'id_tipo_operacion_fe' => 'required|exists:tipo_operacion,codigo',
            'periodo' => 'required|string|max:7',
            'estado' => 'required|string|max:20',
            'fecha_recepcion' => 'nullable|date',
            'tipo_movimiento' => 'required|in:d,h',
            'flete' => 'required|numeric|min:0',
            'forma_pago' => 'required|in:CONTADO,CREDITO',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $movimiento = MovimientoCabecera::updateOrCreate(
            [
                'id_empresa' => $request->id_empresa,
                'serie' => $request->serie,
                'numero' => $request->numero,
                'id_tipo_invoice' => $request->id_tipo_invoice
            ],
            $request->all()
        );

        return response()->json($movimiento->load([
            'empresa',
            'proveedor',
            'tipoDocumento',
            'tipoOperacionPle',
            'tipoOperacionFe'
        ]), 201);
    }

    public function show($id)
    {
        $movimiento = MovimientoCabecera::with([
            'empresa',
            'proveedor',
            'tipoDocumento',
            'tipoOperacionPle',
            'tipoOperacionFe',
            'detalles'
        ])->find($id);
        if (!$movimiento) {
            return response()->json(['message' => 'Movimiento no encontrado'], 404);
        }
        return response()->json($movimiento);
    }

    public function update(Request $request, $id)
    {
        $movimiento = MovimientoCabecera::find($id);
        if (!$movimiento) {
            return response()->json(['message' => 'Movimiento no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'id_empresa' => 'exists:empresas,id',
            'fecha_emision' => 'date',
            'fecha_vencimiento' => 'nullable|date',
            'codigo_anexo' => 'nullable|integer',
            'id_proveedor' => 'nullable|exists:anexos,id',
            'id_tipo_invoice' => 'exists:tipo_documento,codigo',
            'serie' => 'string|max:10',
            'numero' => 'string|max:20',
            'moneda' => 'string|max:3',
            'tipo_cambio' => 'numeric|min:0',
            'valor_compra' => 'numeric|min:0',
            'gratuito' => 'numeric|min:0',
            'igv' => 'numeric|min:0',
            'total' => 'numeric|min:0',
            'total_moneda_base' => 'numeric|min:0',
            'id_tipo_operacion' => 'exists:tipo_operacion_ple,codigo',
            'id_tipo_operacion_fe' => 'exists:catalogofe_17_tipo_operacion,codigo',
            'periodo' => 'string|max:7',
            'estado' => 'string|max:20',
            'fecha_recepcion' => 'nullable|date',
            'tipo_movimiento' => 'in:d,h',
            'flete' => 'numeric|min:0',
            'forma_pago' => 'in:CONTADO,CREDITO',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $movimiento->update($request->all());
        return response()->json($movimiento->load([
            'empresa',
            'proveedor',
            'tipoDocumento',
            'tipoOperacionPle',
            'tipoOperacionFe'
        ]));
    }

    public function destroy($id)
    {
        $movimiento = MovimientoCabecera::find($id);
        if (!$movimiento) {
            return response()->json(['message' => 'Movimiento no encontrado'], 404);
        }

        $movimiento->delete();
        return response()->json(['message' => 'Movimiento eliminado correctamente']);
    }
}
