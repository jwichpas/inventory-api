<?php

namespace App\Http\Controllers\Api\Inventario;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Inventario\Almacen;
use Illuminate\Support\Facades\Validator;

class AlmacenController extends Controller
{
    public function index()
    {
        $almacenes = Almacen::with(['empresa'])->get();
        return response()->json($almacenes);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_empresa' => 'required|exists:empresas,id',
            'nombre' => 'required|string|max:100',
            'pais' => 'required|string|max:100',
            'ciudad' => 'required|string|max:100',
            'direccion' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $almacen = Almacen::updateOrCreate(
            ['id_empresa' => $request->id_empresa, 'nombre' => $request->nombre],
            $request->all()
        );

        return response()->json($almacen->load('empresa'), 201);
    }

    public function show($id)
    {
        $almacen = Almacen::with(['empresa'])->find($id);
        if (!$almacen) {
            return response()->json(['message' => 'Almacén no encontrado'], 404);
        }
        return response()->json($almacen);
    }

    public function update(Request $request, $id)
    {
        $almacen = Almacen::find($id);
        if (!$almacen) {
            return response()->json(['message' => 'Almacén no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'id_empresa' => 'exists:empresas,id',
            'nombre' => 'string|max:100',
            'pais' => 'string|max:100',
            'ciudad' => 'string|max:100',
            'direccion' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $almacen->update($request->all());
        return response()->json($almacen->load('empresa'));
    }

    public function destroy($id)
    {
        $almacen = Almacen::find($id);
        if (!$almacen) {
            return response()->json(['message' => 'Almacén no encontrado'], 404);
        }

        $almacen->delete();
        return response()->json(['message' => 'Almacén eliminado correctamente']);
    }
}
