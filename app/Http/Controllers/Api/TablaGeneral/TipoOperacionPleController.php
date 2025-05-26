<?php

namespace App\Http\Controllers\Api\TablaGeneral;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TablaGeneral\TipoOperacionPle;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;

class TipoOperacionPleController extends Controller
{
    public function index()
    {
        $tipos = Cache::remember('tipos_operacion_ple_all', 1440, function () {
            return TipoOperacionPle::all();
        });
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

        Cache::forget('tipos_operacion_ple_all');
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
        Cache::forget('tipos_operacion_ple_all');
        return response()->json($tipo);
    }

    public function destroy($codigo)
    {
        $tipo = TipoOperacionPle::find($codigo);
        if (!$tipo) {
            return response()->json(['message' => 'Tipo de operación PLE no encontrado'], 404);
        }

        $tipo->delete();
        Cache::forget('tipos_operacion_ple_all');
        return response()->json(['message' => 'Tipo de operación PLE eliminado correctamente']);
    }
}
