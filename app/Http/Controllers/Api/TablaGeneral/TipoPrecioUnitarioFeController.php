<?php

namespace App\Http\Controllers\Api\TablaGeneral;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TablaGeneral\TipoPrecioUnitario;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;

class TipoPrecioUnitarioController extends Controller
{
    public function index()
    {
        // Assuming this controller is for 'TipoPrecioUnitarioFe' despite the class name
        $tipos = Cache::remember('tipos_precio_unitario_fe_all', 1440, function () {
            return TipoPrecioUnitario::all(); // Still uses TipoPrecioUnitario model as per original code
        });
        return response()->json($tipos);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'codigo' => 'required|string|max:2|unique:tipo_precio_unitario',
            'descripcion' => 'required|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $tipo = TipoPrecioUnitario::updateOrCreate(
            ['codigo' => $request->codigo],
            $request->all()
        );

        Cache::forget('tipos_precio_unitario_fe_all');
        return response()->json($tipo, 201);
    }

    public function show($codigo)
    {
        $tipo = TipoPrecioUnitario::find($codigo);
        if (!$tipo) {
            return response()->json(['message' => 'Tipo de precio unitario no encontrado'], 404);
        }
        return response()->json($tipo);
    }

    public function update(Request $request, $codigo)
    {
        $tipo = TipoPrecioUnitario::find($codigo);
        if (!$tipo) {
            return response()->json(['message' => 'Tipo de precio unitario no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'descripcion' => 'string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $tipo->update($request->all());
        Cache::forget('tipos_precio_unitario_fe_all');
        return response()->json($tipo);
    }

    public function destroy($codigo)
    {
        $tipo = TipoPrecioUnitario::find($codigo);
        if (!$tipo) {
            return response()->json(['message' => 'Tipo de precio unitario no encontrado'], 404);
        }

        $tipo->delete();
        Cache::forget('tipos_precio_unitario_fe_all');
        return response()->json(['message' => 'Tipo de precio unitario eliminado correctamente']);
    }
}
