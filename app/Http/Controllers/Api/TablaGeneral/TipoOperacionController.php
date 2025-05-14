<?php

namespace App\Http\Controllers\Api\TablaGeneral;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TablaGeneral\TipoOperacion;
use Illuminate\Support\Facades\Validator;

class TipoOperacionController extends Controller
{
    public function index()
    {
        $tipos = TipoOperacion::all();
        return response()->json($tipos);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'codigo' => 'required|string|max:10|unique:tipo_operacion',
            'descripcion' => 'required|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $tipo = TipoOperacion::updateOrCreate(
            ['codigo' => $request->codigo],
            $request->all()
        );

        return response()->json($tipo, 201);
    }

    public function show($codigo)
    {
        $tipo = TipoOperacion::find($codigo);
        if (!$tipo) {
            return response()->json(['message' => 'Tipo de operación FE no encontrado'], 404);
        }
        return response()->json($tipo);
    }

    public function update(Request $request, $codigo)
    {
        $tipo = TipoOperacion::find($codigo);
        if (!$tipo) {
            return response()->json(['message' => 'Tipo de operación FE no encontrado'], 404);
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
        $tipo = TipoOperacion::find($codigo);
        if (!$tipo) {
            return response()->json(['message' => 'Tipo de operación FE no encontrado'], 404);
        }

        $tipo->delete();
        return response()->json(['message' => 'Tipo de operación FE eliminado correctamente']);
    }
}
