<?php

namespace App\Http\Controllers\Api\Inventario;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Inventario\AlmacenStock;
use App\Models\Inventario\Almacen;
use App\Models\Inventario\Lote;
use Illuminate\Support\Facades\Validator;

class AlmacenStockController extends Controller
{
    public function index()
    {
        $stocks = AlmacenStock::with(['variante', 'almacen', 'lote'])->get();
        return response()->json($stocks);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_variante' => 'required|exists:variantes_producto,id',
            'id_almacen' => 'required|exists:almacenes,id',
            'id_lote' => 'required|exists:lotes,id',
            'stock' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        // Verificar si ya existe un registro con la misma combinación
        $stock = AlmacenStock::where('id_variante', $request->id_variante)
            ->where('id_almacen', $request->id_almacen)
            ->where('id_lote', $request->id_lote)
            ->first();

        if ($stock) {
            $stock->update(['stock' => $request->stock]);
        } else {
            $stock = AlmacenStock::create($request->all());
        }

        return response()->json($stock->load(['variante', 'almacen', 'lote']), 201);
    }

    public function show($id)
    {
        // Para mostrar un stock específico necesitaríamos cambiar la estructura de la tabla
        // ya que actualmente es una tabla compuesta sin ID único
        return response()->json(['message' => 'No implementado'], 501);
    }

    public function update(Request $request, $id_variante, $id_almacen, $id_lote)
    {
        $stock = AlmacenStock::where('id_variante', $id_variante)
            ->where('id_almacen', $id_almacen)
            ->where('id_lote', $id_lote)
            ->first();

        if (!$stock) {
            return response()->json(['message' => 'Stock no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'stock' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $stock->update($request->only('stock'));
        return response()->json($stock->load(['variante', 'almacen', 'lote']));
    }

    public function destroy($id_variante, $id_almacen, $id_lote)
    {
        $stock = AlmacenStock::where('id_variante', $id_variante)
            ->where('id_almacen', $id_almacen)
            ->where('id_lote', $id_lote)
            ->first();

        if (!$stock) {
            return response()->json(['message' => 'Stock no encontrado'], 404);
        }

        $stock->delete();
        return response()->json(['message' => 'Stock eliminado correctamente']);
    }
}
