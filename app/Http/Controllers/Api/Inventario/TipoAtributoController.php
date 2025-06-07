<?php

namespace App\Http\Controllers\Api\Inventario;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Inventario\TipoAtributos;
use Illuminate\Support\Facades\Validator;

class TipoAtributoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = TipoAtributos::query();

        // Filtro por empresa si se proporciona
        if ($request->has('id_empresa')) {
            $query->where('id_empresa', $request->id_empresa);
        }

        // Búsqueda por nombre
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Paginación
        $perPage = $request->has('per_page') ? (int)$request->per_page : 15;
        $tiposAtributos = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $tiposAtributos
        ]);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_empresa' => 'required|exists:empresas,id',
            'name' => 'required|string|max:50'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $tipoAtributo = TipoAtributos::create([
                'id_empresa' => $request->id_empresa,
                'name' => $request->name
            ]);

            return response()->json([
                'success' => true,
                'data' => $tipoAtributo,
                'message' => 'Tipo de atributo creado exitosamente'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el tipo de atributo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $tipoAtributo = TipoAtributos::find($id);

        if (!$tipoAtributo) {
            return response()->json([
                'success' => false,
                'message' => 'Tipo de atributo no encontrado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $tipoAtributo
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $tipoAtributo = TipoAtributos::find($id);

        if (!$tipoAtributo) {
            return response()->json([
                'success' => false,
                'message' => 'Tipo de atributo no encontrado'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:50'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            if ($request->has('name')) {
                $tipoAtributo->name = $request->name;
            }

            $tipoAtributo->save();

            return response()->json([
                'success' => true,
                'data' => $tipoAtributo,
                'message' => 'Tipo de atributo actualizado exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el tipo de atributo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $tipoAtributo = TipoAtributos::find($id);

        if (!$tipoAtributo) {
            return response()->json([
                'success' => false,
                'message' => 'Tipo de atributo no encontrado'
            ], 404);
        }

        try {
            $tipoAtributo->delete();

            return response()->json([
                'success' => true,
                'message' => 'Tipo de atributo eliminado exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el tipo de atributo: ' . $e->getMessage()
            ], 500);
        }
    }
}
