<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Empresa;
use Illuminate\Support\Facades\Validator;

class EmpresaController extends Controller
{
    public function index()
    {
        $empresas = Empresa::all();
        return response()->json($empresas);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:100|unique:empresas',
            'ruc' => 'required|string|max:20|unique:empresas',
            'direccion' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $empresa = Empresa::create($request->all());
        return response()->json($empresa, 201);
    }

    public function show($id)
    {
        $empresa = Empresa::find($id);
        if (!$empresa) {
            return response()->json(['message' => 'Empresa no encontrada'], 404);
        }
        return response()->json($empresa);
    }

    public function update(Request $request, $id)
    {
        $empresa = Empresa::find($id);
        if (!$empresa) {
            return response()->json(['message' => 'Empresa no encontrada'], 404);
        }

        $validator = Validator::make($request->all(), [
            'nombre' => 'string|max:100|unique:empresas,nombre,' . $id,
            'ruc' => 'string|max:20|unique:empresas,ruc,' . $id,
            'direccion' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $empresa->update($request->all());
        return response()->json($empresa);
    }

    public function destroy($id)
    {
        $empresa = Empresa::find($id);
        if (!$empresa) {
            return response()->json(['message' => 'Empresa no encontrada'], 404);
        }

        $empresa->delete();
        return response()->json(['message' => 'Empresa eliminada correctamente']);
    }
}
