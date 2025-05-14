<?php

namespace App\Http\Controllers\Api\TablaGeneral;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TablaGeneral\TipoOperacionPle;
use Illuminate\Support\Facades\Validator;

class TipoOperacionPleController extends Controller
{
    public function index()
    {
        $tipos = TipoOperacionPle::all();
        return response()->json($tipos);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'codigo' => 'required|string|max:2|unique:tipo_operacion_ple',
            'descripcion' => 'required|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $tipo = TipoOperacionPle::updateOrCreate(
            ['codigo' => $request->codigo],
            $request->all()
        );

        return response()->json($tipo, 201);
    }

    public function show($codigo)
    {
        $tipo = TipoOperacionPle::find($codigo);
        if (!$tipo) {
            return response()->json(['message' => 'Tipo de operación PLE no encontrado'], 404);
        }
        return response()->json($tipo);
    }

    public function update(Request $request, $codigo)
    {
        $tipo = TipoOperacionPle::find($codigo);
        if (!$tipo) {
            return response()->json(['message' => 'Tipo de operación PLE no encontrado'], 404);
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
        $tipo = TipoOperacionPle::find($codigo);
        if (!$tipo) {
            return response()->json(['message' => 'Tipo de operación PLE no encontrado'], 404);
        }

        $tipo->delete();
        return response()->json(['message' => 'Tipo de operación PLE eliminado correctamente']);
    }
}
