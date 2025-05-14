<?php

namespace App\Http\Controllers\Api\TablaGeneral;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TablaGeneral\TipoAfectacionIgv;
use Illuminate\Support\Facades\Validator;

class TipoAfectacionIgvController extends Controller
{
    public function index()
    {
        $tipos = TipoAfectacionIgv::all();
        return response()->json($tipos);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'codigo' => 'required|string|max:10|unique:catalogofe_07_tipoafectacion_igv',
            'descripcion' => 'required|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $tipo = TipoAfectacionIgv::updateOrCreate(
            ['codigo' => $request->codigo],
            $request->all()
        );

        return response()->json($tipo, 201);
    }

    public function show($codigo)
    {
        $tipo = TipoAfectacionIgv::find($codigo);
        if (!$tipo) {
            return response()->json(['message' => 'Tipo de afectación IGV no encontrado'], 404);
        }
        return response()->json($tipo);
    }

    public function update(Request $request, $codigo)
    {
        $tipo = TipoAfectacionIgv::find($codigo);
        if (!$tipo) {
            return response()->json(['message' => 'Tipo de afectación IGV no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'descripcion' => 'string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $tipo->update($request->all());
        return response()->json($tipo);
    }

    public function destroy($codigo)
    {
        $tipo = TipoAfectacionIgv::find($codigo);
        if (!$tipo) {
            return response()->json(['message' => 'Tipo de afectación IGV no encontrado'], 404);
        }

        $tipo->delete();
        return response()->json(['message' => 'Tipo de afectación IGV eliminado correctamente']);
    }
}
