<?php

namespace App\Http\Controllers\Api\TablaGeneral;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TablaGeneral\TipoDocumento;
use Illuminate\Support\Facades\Validator;

class TipoDocumentoController extends Controller
{
    public function index()
    {
        $tipos = TipoDocumento::all();
        return response()->json($tipos);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'codigo' => 'required|string|max:10|unique:tipo_documento',
            'descripcion' => 'required|string|max:100',
            'simbolo' => 'required|string|max:10',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $tipo = TipoDocumento::updateOrCreate(
            ['codigo' => $request->codigo],
            $request->all()
        );

        return response()->json($tipo, 201);
    }

    public function show($codigo)
    {
        $tipo = TipoDocumento::find($codigo);
        if (!$tipo) {
            return response()->json(['message' => 'Tipo de documento no encontrado'], 404);
        }
        return response()->json($tipo);
    }

    public function update(Request $request, $codigo)
    {
        $tipo = TipoDocumento::find($codigo);
        if (!$tipo) {
            return response()->json(['message' => 'Tipo de documento no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'descripcion' => 'string|max:100',
            'simbolo' => 'string|max:10',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $tipo->update($request->all());
        return response()->json($tipo);
    }

    public function destroy($codigo)
    {
        $tipo = TipoDocumento::find($codigo);
        if (!$tipo) {
            return response()->json(['message' => 'Tipo de documento no encontrado'], 404);
        }

        $tipo->delete();
        return response()->json(['message' => 'Tipo de documento eliminado correctamente']);
    }
}
