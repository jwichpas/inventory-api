<?php

namespace App\Http\Controllers\Api\Inventario;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Inventario\UnidadMedida;
use Illuminate\Support\Facades\Validator;

class UnidadMedidaController extends Controller
{
    public function index()
    {
        $unidades = UnidadMedida::all();
        return response()->json($unidades);
    }

    public function umedidaxempresa($idEmpresa)
    {
        $categories = UnidadMedida::with('empresa')
            ->where('id_empresa', $idEmpresa)
            ->get();

        return response()->json($categories);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'codigo_sunat' => 'required|string|max:255|unique:unidad_medidas',
            'nombre_sunat' => 'required|string|max:255',
            'simbolo' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $unidad = UnidadMedida::updateOrCreate(
            ['codigo_sunat' => $request->codigo_sunat],
            $request->all()
        );

        return response()->json($unidad, 201);
    }

    public function show($id)
    {
        $unidad = UnidadMedida::find($id);
        if (!$unidad) {
            return response()->json(['message' => 'Unidad de medida no encontrada'], 404);
        }
        return response()->json($unidad);
    }

    public function update(Request $request, $id)
    {
        $unidad = UnidadMedida::find($id);
        if (!$unidad) {
            return response()->json(['message' => 'Unidad de medida no encontrada'], 404);
        }

        $validator = Validator::make($request->all(), [
            'codigo_sunat' => 'string|max:255|unique:unidad_medida,codigo_sunat,' . $id,
            'nombre_sunat' => 'string|max:255',
            'simbolo' => 'string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $unidad->update($request->all());
        return response()->json($unidad);
    }

    public function destroy($id)
    {
        $unidad = UnidadMedida::find($id);
        if (!$unidad) {
            return response()->json(['message' => 'Unidad de medida no encontrada'], 404);
        }

        $unidad->delete();
        return response()->json(['message' => 'Unidad de medida eliminada correctamente']);
    }
}
