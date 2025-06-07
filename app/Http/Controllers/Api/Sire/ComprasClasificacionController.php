<?php

namespace App\Http\Controllers\Api\Sire;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sire\SireComprasClasificacion;
use App\Models\Sire\SireCompras;
use Illuminate\Support\Facades\Validator;

class ComprasClasificacionController extends Controller
{
    /**
     * Asignar clasificación a una compra
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'compra_id' => 'required|exists:sire_compras,id',
            'tipo_proveedor' => 'required|in:mercaderia,flete,gastos_ventas,gastos_administracion',
            'estado' => 'sometimes|in:pendiente,aplicado'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $clasificacion = SireComprasClasificacion::create([
            'compra_id' => $request->compra_id,
            'tipo_proveedor' => $request->tipo_proveedor,
            'estado' => $request->estado ?? 'pendiente'
        ]);

        return response()->json([
            'success' => true,
            'data' => $clasificacion
        ], 201);
    }

    /**
     * Actualizar clasificación
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'tipo_proveedor' => 'sometimes|in:mercaderia,flete,gastos_ventas,gastos_administracion',
            'estado' => 'sometimes|in:pendiente,aplicado'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $clasificacion = SireComprasClasificacion::find($id);

        if (!$clasificacion) {
            return response()->json([
                'success' => false,
                'message' => 'Clasificación no encontrada'
            ], 404);
        }

        $clasificacion->update($request->all());

        return response()->json([
            'success' => true,
            'data' => $clasificacion
        ]);
    }

    /**
     * Obtener clasificaciones de una compra
     */
    public function show($compraId)
    {
        $compra = SireCompras::with('clasificaciones')->find($compraId);

        if (!$compra) {
            return response()->json([
                'success' => false,
                'message' => 'Compra no encontrada'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $compra->clasificaciones
        ]);
    }
}
