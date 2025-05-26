<?php

namespace App\Http\Controllers\Api\TablaGeneral;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TablaGeneral\TipoDocumento;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;

class TipoDocumentoController extends Controller
{
    public function index()
    {
        $tipos = Cache::remember('tipos_documento_all', 1440, function () {
            return TipoDocumento::all();
        });
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

        Cache::forget('tipos_documento_all');
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
        Cache::forget('tipos_documento_all');
        return response()->json($tipo);
    }

    public function destroy($codigo)
    {
        $tipo = TipoDocumento::find($codigo);
        if (!$tipo) {
            return response()->json(['message' => 'Tipo de documento no encontrado'], 404);
        }

        $tipo->delete();
        Cache::forget('tipos_documento_all');
        return response()->json(['message' => 'Tipo de documento eliminado correctamente']);
    }
}
