<?php

namespace App\Http\Controllers\Api\Inventario;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Inventario\VarianteProduct;
use Illuminate\Support\Facades\Validator;

class VarianteProductoController extends Controller
{
    public function index()
    {
        $variantes = VarianteProduct::with(['producto', 'unidadMedida', 'atributos', 'lotes'])->get();
        return response()->json($variantes);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_producto' => 'required|exists:products,id',
            'sku' => 'required|string|max:100|unique:variantes_producto',
            'codigo_sunat' => 'nullable|string|max:13|unique:variantes_producto',
            'ean13' => 'nullable|string|max:13|unique:variantes_producto',
            'ean14' => 'nullable|string|max:14|unique:variantes_producto',
            'imagen' => 'nullable|string|max:255',
            'costo' => 'numeric|min:0',
            'precio' => 'numeric|min:0',
            'id_unidad_medida' => 'required|exists:unidad_medida,id',
            'atributos' => 'nullable|array',
            'atributos.*' => 'exists:atributos,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $variante = VarianteProduct::updateOrCreate(
            ['sku' => $request->sku],
            $request->except('atributos')
        );

        if ($request->has('atributos')) {
            $variante->atributos()->sync($request->atributos);
        }

        return response()->json($variante->load(['producto', 'unidadMedida', 'atributos']), 201);
    }

    public function show($id)
    {
        $variante = VarianteProduct::with(['producto', 'unidadMedida', 'atributos', 'lotes'])->find($id);
        if (!$variante) {
            return response()->json(['message' => 'Variante no encontrada'], 404);
        }
        return response()->json($variante);
    }

    public function update(Request $request, $id)
    {
        $variante = VarianteProduct::find($id);
        if (!$variante) {
            return response()->json(['message' => 'Variante no encontrada'], 404);
        }

        $validator = Validator::make($request->all(), [
            'id_producto' => 'exists:products,id',
            'sku' => 'string|max:100|unique:variantes_producto,sku,' . $id,
            'codigo_sunat' => 'nullable|string|max:13|unique:variantes_producto,codigo_sunat,' . $id,
            'ean13' => 'nullable|string|max:13|unique:variantes_producto,ean13,' . $id,
            'ean14' => 'nullable|string|max:14|unique:variantes_producto,ean14,' . $id,
            'imagen' => 'nullable|string|max:255',
            'costo' => 'numeric|min:0',
            'precio' => 'numeric|min:0',
            'id_unidad_medida' => 'exists:unidad_medida,id',
            'atributos' => 'nullable|array',
            'atributos.*' => 'exists:atributos,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $variante->update($request->except('atributos'));

        if ($request->has('atributos')) {
            $variante->atributos()->sync($request->atributos);
        }

        return response()->json($variante->load(['producto', 'unidadMedida', 'atributos']));
    }

    public function destroy($id)
    {
        $variante = VarianteProduct::find($id);
        if (!$variante) {
            return response()->json(['message' => 'Variante no encontrada'], 404);
        }

        $variante->delete();
        return response()->json(['message' => 'Variante eliminada correctamente']);
    }
}
