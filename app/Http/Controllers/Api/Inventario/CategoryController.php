<?php

namespace App\Http\Controllers\Api\Inventario;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use  App\Models\Inventario\Categorie;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index($idEmpresa)
    {
        $categories = Categorie::with('empresa')
            ->where('id_empresa', $idEmpresa)
            ->get();

        return response()->json($categories);
    }
    public function categoriaxempresa($idEmpresa)
    {
        $categories = Categorie::with('empresa')
            ->where('id_empresa', $idEmpresa)
            ->get();

        return response()->json($categories);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_empresa' => 'required|exists:empresas,id',
            'codigo' => 'nullable|string|max:50',
            'name' => 'required|string|max:50',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $data = $request->all();

        // Procesar la imagen si existe
        if ($request->hasFile('image')) {
            $image = $request->file('image');

            // Generar nombre único para la imagen
            $imageName = Str::random(20) . '.' . $image->getClientOriginalExtension();

            // Guardar en storage/app/public/categories
            $path = $image->storeAs('public/categories', $imageName);

            // Almacenar la ruta relativa (sin 'public/')
            $data['image'] = 'categories/' . $imageName;
        }

        $category = Categorie::updateOrCreate(
            ['id_empresa' => $request->id_empresa, 'name' => $request->name],
            $data
        );

        return response()->json($category, 201);
    }

    public function show($id)
    {
        $category = Categorie::with('empresa')->find($id);
        if (!$category) {
            return response()->json(['message' => 'Categoría no encontrada'], 404);
        }
        return response()->json($category);
    }

    public function update(Request $request, $id)
    {
        $category = Categorie::find($id);
        if (!$category) {
            return response()->json(['message' => 'Categoría no encontrada'], 404);
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

        $category->update($request->all());
        return response()->json($category);
    }

    public function destroy($id)
    {
        $category = Categorie::find($id);
        if (!$category) {
            return response()->json(['message' => 'Categoría no encontrada'], 404);
        }

        $category->delete();
        return response()->json(['message' => 'Categoría eliminada correctamente']);
    }

    public function validateCode(Request $request)
    {
        $validated = $request->validate([
            'codigo' => 'required|string|max:50',
            'enterprise_id' => 'required|integer'
        ]);

        $exists = Categorie::where('codigo', $validated['codigo'])
            ->where('id_empresa', $validated['enterprise_id'])
            ->exists();

        return response()->json([
            'valid' => !$exists,
            'message' => $exists ? 'Código duplicado (validación local)' : 'Código válido'
        ]);
    }
}
