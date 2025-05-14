<?php

namespace App\Http\Controllers\Api\Inventario;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Inventario\Lote;
use Illuminate\Support\Facades\Validator;

class LoteController extends Controller
{
    public function index()
    {
        $lotes = Lote::with(['variante'])->get();
        return response()->json($lotes);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_variante' => 'required|exists:variantes_producto,id',
            'codigo_lote' => 'required|string|max:50',
            'fecha_vencimiento' => 'required|date',
            'fecha_fabricacion' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $lote = Lote::updateOrCreate(
            ['id_variante' => $request->id_variante, 'codigo_lote' => $request->codigo_lote],
            $request->all()
        );

        return response()->json($lote->load('variante'), 201);
    }

    public function show($id)
    {
        $lote = Lote::with(['variante'])->find($id);
        if (!$lote) {
            return response()->json(['message' => 'Lote no encontrado'], 404);
        }
        return response()->json($lote);
    }

    public function update(Request $request, $id)
    {
        $lote = Lote::find($id);
        if (!$lote) {
            return response()->json(['message' => 'Lote no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'id_variante' => 'exists:variantes_producto,id',
            'codigo_lote' => 'string|max:50',
            'fecha_vencimiento' => 'date',
            'fecha_fabricacion' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $lote->update($request->all());
        return response()->json($lote->load('variante'));
    }

    public function destroy($id)
    {
        $lote = Lote::find($id);
        if (!$lote) {
            return response()->json(['message' => 'Lote no encontrado'], 404);
        }

        $lote->delete();
        return response()->json(['message' => 'Lote eliminado correctamente']);
    }
}
