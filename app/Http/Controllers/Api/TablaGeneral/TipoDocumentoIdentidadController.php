<?php

namespace App\Http\Controllers\Api\TablaGeneral;

use App\Http\Controllers\Controller;
use App\Models\TablaGeneral\TipoDocumentoIdentidad;
use Illuminate\Http\Request;

class TipoDocumentoIdentidadController extends Controller
{
    public function index()
    {
        $tiposDocumento = TipoDocumentoIdentidad::all();
        return response()->json($tiposDocumento);
    }
    public function store(Request $request)
    {
        $request->validate([
            'codigo' => 'required|string|max:1|unique:tipo_documento_identidad',
            'name' => 'required|string|max:255',
        ]);

        $tipoDocumento = TipoDocumentoIdentidad::updateOrCreate(
            ['codigo' => $request->codigo],
            $request->all()
        );

        return response()->json($tipoDocumento, 201);
    }
    public function show($codigo)
    {
        $tipoDocumento = TipoDocumentoIdentidad::find($codigo);
        if (!$tipoDocumento) {
            return response()->json(['message' => 'Tipo de documento de identidad no encontrado'], 404);
        }
        return response()->json($tipoDocumento);
    }
    public function update(Request $request, $codigo)
    {
        $request->validate([
            'codigo' => 'required|string|max:1|unique:tipo_documento_identidad,codigo,' . $codigo,
            'name' => 'required|string|max:255',
        ]);
        $tipoDocumento = TipoDocumentoIdentidad::find($codigo);
        if (!$tipoDocumento) {
            return response()->json(['message' => 'Tipo de documento de identidad no encontrado'], 404);
            }
        $tipoDocumento->update($request->all());
        return response()->json($tipoDocumento, 200);
    }

    public function destroy($codigo)
    {
        $tipoDocumento = TipoDocumentoIdentidad::find($codigo);
        if (!$tipoDocumento) {
            return response()->json(['message' => 'Tipo de documento de identidad no encontrado'], 404);
            }
        $tipoDocumento->delete();
        return response()->json(['message' => 'Tipo de documento de identidad eliminado'], 200);
    }
}
