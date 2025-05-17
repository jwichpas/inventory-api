<?php

namespace App\Http\Controllers\Api\Inventario;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Inventario\Brand;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class BrandController extends Controller
{
    public function index()
    {
        $brands = Brand::with('empresa')->get();
        return response()->json($brands);
    }

    public function storeold(Request $request)
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
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_empresa' => 'required|exists:empresas,id',
            'codigo' => 'nullable|string|max:50|unique:brands,codigo,NULL,id,id_empresa,' . $request->id_empresa,
            'name' => 'required|string|max:50',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048', // Validación para archivos de imagen
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $data = $request->except('image');

        // Procesar la imagen si existe
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();

            // Guardar en storage/app/public (accesible públicamente)
            $path = $file->storeAs('public/brands', $fileName);

            // URL accesible públicamente
            $data['image'] = 'brands/' . $fileName;
        } elseif ($request->has('image_url')) {
            // Si se envía una URL de imagen existente
            $data['image'] = $request->image_url;
        }



        $brand = Brand::updateOrCreate(
            ['id_empresa' => $request->id_empresa, 'name' => $request->name],
            $data
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

    public function validateCode(Request $request)
    {
        $request->validate([
            'codigo' => 'required|string|max:50',
            'enterprise_id' => 'required|exists:empresas,id'
        ]);

        $exists = Brand::where('codigo', $request->codigo)
            ->where('id_empresa', $request->enterprise_id)
            ->exists();

        return response()->json([
            'is_valid' => !$exists,
            'message' => $exists ? 'El código ya existe para esta empresa' : 'Código disponible'
        ]);
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
