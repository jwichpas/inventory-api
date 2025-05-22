<?php

namespace App\Http\Controllers\Api\Inventario;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Inventario\Brand;
use Illuminate\Support\Facades\Validator;

class BrandController extends Controller
{
    public function index()
    {
        $brands = Brand::with('empresa')->get();
        return response()->json($brands);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_empresa' => 'required|exists:empresas,id',
            'codigo' => 'nullable|string|max:50',
            'name' => 'required|string|max:50',
            'image' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $brand = Brand::updateOrCreate(
            ['id_empresa' => $request->id_empresa, 'name' => $request->name],
            $request->all()
        );

        return response()->json($brand, 201);
    }

    public function show($id)
    {
        $brand = Brand::with('empresa')->find($id);
        if (!$brand) {
            return response()->json(['message' => 'Marca no encontrada'], 404);
        }
        return response()->json($brand);
    }

    public function update(Request $request, $id)
    {
        $brand = Brand::find($id);
        if (!$brand) {
            return response()->json(['message' => 'Marca no encontrada'], 404);
        }

        $validator = Validator::make($request->all(), [
            'id_empresa' => 'exists:empresas,id',
            'codigo' => 'nullable|string|max:50',
            'name' => 'string|max:50',
            'image' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $brand->update($request->all());
        return response()->json($brand);
    }

    public function destroy($id)
    {
        $brand = Brand::find($id);
        if (!$brand) {
            return response()->json(['message' => 'Marca no encontrada'], 404);
        }

        $brand->delete();
        return response()->json(['message' => 'Marca eliminada correctamente']);
    }
}
